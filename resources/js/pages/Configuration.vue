<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';

interface FormState {
    command: string;
    description: string;
}

const form = reactive<FormState>({
    command: '',
    description: '',
});

const file = ref<File | null>(null);
const previewUrl = ref<string | null>(null);
const submitting = ref(false);
const errors = ref<Record<string, string>>({});
const success = ref(false);
const serverError = ref<string | null>(null);

function readCsrfToken(): string {
    // Laravel stores the CSRF token in the (URL-encoded) XSRF-TOKEN cookie.
    const match = document.cookie
        .split('; ')
        .find((entry) => entry.startsWith('XSRF-TOKEN='));

    return match ? decodeURIComponent(match.slice('XSRF-TOKEN='.length)) : '';
}

function onFileChange(event: Event) {
    const input = event.target as HTMLInputElement;
    const selected = input.files?.[0] ?? null;
    file.value = selected ?? null;

    if (previewUrl.value) {
        URL.revokeObjectURL(previewUrl.value);
        previewUrl.value = null;
    }

    if (selected) {
        previewUrl.value = URL.createObjectURL(selected);
    }
}

function validate(): boolean {
    errors.value = {};

    if (!form.command.trim()) {
        errors.value.command = 'The command is required.';
    }

    if (!form.description.trim()) {
        errors.value.description = 'The description is required.';
    }

    if (file.value && file.value.type !== 'image/png') {
        errors.value.file = 'The image must be a PNG file.';
    }

    return Object.keys(errors.value).length === 0;
}

async function submit() {
    errors.value = {};
    serverError.value = null;
    success.value = false;

    if (!validate()) {
        return;
    }

    submitting.value = true;

    try {
        const payload = new FormData();
        payload.append('command', form.command.trim());
        payload.append('description', form.description.trim());

        if (file.value) {
            payload.append('file', file.value);
        }

        const response = await fetch('/commands', {
            method: 'POST',
            headers: {
                'X-XSRF-TOKEN': readCsrfToken(),
                // Ask for a JSON response so validation failures come back as
                // 422 with per-field errors, instead of a redirect to 302.
                Accept: 'application/json',
            },
            body: payload,
        });

        const data = await response.json().catch(() => ({}));

        if (response.ok && data.success) {
            success.value = true;
            form.command = '';
            form.description = '';
            file.value = null;

            if (previewUrl.value) {
                URL.revokeObjectURL(previewUrl.value);
                previewUrl.value = null;
            }

            const fileInput =
                document.querySelector<HTMLInputElement>('#command-file');

            if (fileInput) {
                fileInput.value = '';
            }
        } else if (response.status === 422 && data.errors) {
            errors.value = data.errors;
        } else {
            serverError.value =
                data.message ??
                'Something went wrong while creating the command.';
        }
    } catch {
        serverError.value =
            'Could not reach the backend. Is the server running?';
    } finally {
        submitting.value = false;
    }
}
</script>

<template>
    <div
        class="relative min-h-screen overflow-hidden bg-zinc-950 text-zinc-100"
    >
        <!-- Decorative background glows -->
        <div aria-hidden="true" class="pointer-events-none absolute inset-0">
            <div
                class="absolute -top-24 left-1/4 h-80 w-xl -translate-x-1/2 rounded-full bg-violet-600/15 blur-3xl"
            ></div>
            <div
                class="absolute right-0 bottom-0 h-72 w-72 rounded-full bg-fuchsia-600/10 blur-3xl"
            ></div>
        </div>

        <main class="relative mx-auto w-full max-w-2xl px-6 py-10">
            <!-- Top bar -->
            <header class="mb-10 flex items-center gap-4">
                <button
                    type="button"
                    @click="router.visit('/')"
                    class="inline-flex items-center gap-2 rounded-xl border border-zinc-800 bg-zinc-900/60 px-4 py-2 text-sm font-medium text-zinc-300 transition-colors hover:border-zinc-600 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400"
                >
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="h-4 w-4"
                    >
                        <path d="m15 18-6-6 6-6" />
                    </svg>
                    Back
                </button>

                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-white">
                        Configuration
                    </h1>
                    <p class="mt-1 text-sm font-medium text-zinc-500">
                        Create a new command for your deck
                    </p>
                </div>
            </header>

            <!-- Success banner -->
            <Transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0 -translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-2"
            >
                <div
                    v-if="success"
                    class="mb-6 flex items-center gap-3 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-5 py-4 text-emerald-300"
                >
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="h-5 w-5 shrink-0"
                    >
                        <circle cx="12" cy="12" r="10" />
                        <path d="m8.5 12.5 2.5 2.5 5-5" />
                    </svg>
                    <p class="text-sm font-medium">
                        Command created successfully! It will appear on your
                        deck.
                    </p>
                </div>
            </Transition>

            <!-- Server error banner -->
            <div
                v-if="serverError"
                class="mb-6 flex items-center gap-3 rounded-2xl border border-red-500/30 bg-red-500/10 px-5 py-4 text-red-300"
            >
                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="h-5 w-5 shrink-0"
                >
                    <circle cx="12" cy="12" r="10" />
                    <path d="M12 8v4M12 16h.01" />
                </svg>
                <p class="text-sm font-medium">{{ serverError }}</p>
            </div>

            <!-- Create form -->
            <form
                @submit.prevent="submit"
                class="overflow-hidden rounded-3xl border border-zinc-800 bg-zinc-900/60 shadow-xl shadow-black/30"
            >
                @csrf
                <div class="space-y-6 p-7">
                    <!-- Image upload -->
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-zinc-300"
                        >
                            Icon
                            <span class="text-zinc-500">(optional · PNG)</span>
                        </label>

                        <label
                            class="relative flex aspect-3/2 w-full cursor-pointer items-center justify-center overflow-hidden rounded-2xl border-2 border-dashed border-zinc-700 bg-zinc-950/50 transition-colors hover:border-violet-400/60"
                        >
                            <input
                                id="command-file"
                                type="file"
                                accept="image/png"
                                class="sr-only"
                                @change="onFileChange"
                            />

                            <template v-if="previewUrl">
                                <img
                                    :src="previewUrl"
                                    alt="Icon preview"
                                    class="h-full w-full object-cover"
                                />
                                <span
                                    class="absolute inset-0 flex items-center justify-center bg-black/40 text-white opacity-0 transition-opacity group-hover:opacity-100"
                                >
                                    <span
                                        class="rounded-lg bg-black/60 px-3 py-1.5 text-xs font-medium"
                                    >
                                        Click to change
                                    </span>
                                </span>
                            </template>

                            <template v-else>
                                <span
                                    class="flex flex-col items-center gap-2 text-zinc-500"
                                >
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="h-8 w-8"
                                    >
                                        <path d="M12 5v14" />
                                        <path d="m5 12 7-7 7 7" />
                                    </svg>
                                    <span class="text-sm font-medium"
                                        >Upload a PNG icon</span
                                    >
                                </span>
                            </template>
                        </label>

                        <p v-if="errors.file" class="mt-2 text-sm text-red-400">
                            {{ errors.file }}
                        </p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label
                            for="description"
                            class="mb-2 block text-sm font-medium text-zinc-300"
                        >
                            Description
                        </label>
                        <input
                            id="description"
                            v-model="form.description"
                            type="text"
                            placeholder="e.g. Open Spotify"
                            class="w-full rounded-xl border border-zinc-700 bg-zinc-950/60 px-4 py-3 text-sm text-white placeholder-zinc-600 transition-colors focus:border-violet-400 focus:ring-2 focus:ring-violet-400/40 focus:outline-none"
                        />
                        <p
                            v-if="errors.description"
                            class="mt-2 text-sm text-red-400"
                        >
                            {{ errors.description }}
                        </p>
                    </div>

                    <!-- Command -->
                    <div>
                        <label
                            for="command"
                            class="mb-2 block text-sm font-medium text-zinc-300"
                        >
                            Command
                        </label>
                        <input
                            id="command"
                            v-model="form.command"
                            type="text"
                            placeholder="e.g. spotify --start"
                            class="w-full rounded-xl border border-zinc-700 bg-zinc-950/60 px-4 py-3 font-mono text-sm text-white placeholder-zinc-600 transition-colors focus:border-violet-400 focus:ring-2 focus:ring-violet-400/40 focus:outline-none"
                        />
                        <p
                            v-if="errors.command"
                            class="mt-2 text-sm text-red-400"
                        >
                            {{ errors.command }}
                        </p>
                        <p class="mt-2 text-xs text-zinc-500">
                            The shell command that runs when this button is
                            pressed on your deck.
                        </p>
                    </div>
                </div>

                <div
                    class="flex items-center justify-end gap-3 border-t border-zinc-800 bg-zinc-950/40 px-7 py-5"
                >
                    <button
                        type="button"
                        @click="router.visit('/deck')"
                        class="rounded-xl border border-zinc-700 px-5 py-2.5 text-sm font-semibold text-zinc-300 transition-colors hover:border-zinc-500 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400"
                    >
                        View deck
                    </button>
                    <button
                        type="submit"
                        :disabled="submitting"
                        class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-6 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-violet-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-400 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <svg
                            v-if="!submitting"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="h-4 w-4"
                        >
                            <path d="M12 5v14" />
                            <path d="m5 12 7-7 7 7" />
                        </svg>
                        <svg
                            v-else
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.5"
                            stroke-linecap="round"
                            class="h-4 w-4 animate-spin"
                        >
                            <path d="M21 12a9 9 0 1 1-6.22-8.56" />
                        </svg>
                        {{ submitting ? 'Creating…' : 'Create command' }}
                    </button>
                </div>
            </form>
        </main>
    </div>
</template>
