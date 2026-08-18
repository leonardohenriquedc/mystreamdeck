<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { cn } from '../lib/utils';

interface DeckCommand {
    id: number;
    code: string;
    command: string;
    description: string;
    image: string;
    created_at: string;
    updated_at: string;
}

const commands = ref<DeckCommand[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);
const executingCode = ref<string | null>(null);

function imageUrl(command: DeckCommand): string | null {
    return command.image ? `${command.image}` : null;
}

async function loadCommands() {
    loading.value = true;
    error.value = null;

    try {
        const response = await fetch('/commands');
        const data = await response.json();

        if (data.success && Array.isArray(data.commands)) {
            commands.value = data.commands;
        } else {
            error.value = 'The backend returned an unexpected response.';
        }
    } catch {
        error.value = 'Could not reach the backend. Is the server running?';
    } finally {
        loading.value = false;
    }
}

async function execute(command: DeckCommand) {
    if (executingCode.value) {
        return;
    }

    executingCode.value = command.code;

    try {
        // Backend resolves the command by `code` (passed as a query param)
        // and ignores the URL segment.
        await fetch(
            `/command/${command.code}?code=${encodeURIComponent(command.code)}`,
            {
                method: 'GET',
            },
        );
    } catch {
        // Tolerate network hiccups on execution; the button just lights up.
    } finally {
        setTimeout(() => {
            executingCode.value = null;
        }, 250);
    }
}

onMounted(loadCommands);
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
                class="absolute right-0 bottom-0 h-72 w-72 rounded-full bg-cyan-500/10 blur-3xl"
            ></div>
        </div>

        <main class="relative mx-auto w-full max-w-6xl px-6 py-10">
            <!-- Top bar -->
            <header
                class="mb-10 flex flex-wrap items-center justify-between gap-4"
            >
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

                <div class="text-center">
                    <h1 class="text-2xl font-bold tracking-tight text-white">
                        Your Deck
                    </h1>
                    <p class="mt-1 text-sm font-medium text-zinc-500">
                        {{
                            loading
                                ? 'Loading commands…'
                                : `${commands.length} command${commands.length === 1 ? '' : 's'} ready`
                        }}
                    </p>
                </div>

                <button
                    type="button"
                    @click="loadCommands"
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
                        <path d="M21 12a9 9 0 1 1-2.64-6.36" />
                        <path d="M21 3v6h-6" />
                    </svg>
                    Refresh
                </button>
            </header>

            <!-- Loading state -->
            <div
                v-if="loading"
                class="flex flex-col items-center justify-center gap-4 py-24 text-zinc-400"
            >
                <div
                    class="h-10 w-10 animate-spin rounded-full border-2 border-violet-400 border-t-transparent"
                ></div>
                <p class="text-sm">Loading your deck…</p>
            </div>

            <!-- Error state -->
            <div
                v-else-if="error"
                class="mx-auto max-w-md rounded-3xl border border-zinc-800 bg-zinc-900/60 p-8 text-center"
            >
                <div
                    class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-red-500/20 text-red-400"
                >
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="h-6 w-6"
                    >
                        <circle cx="12" cy="12" r="10" />
                        <path d="M12 8v4M12 16h.01" />
                    </svg>
                </div>
                <p class="font-medium text-white">Something went wrong</p>
                <p class="mt-2 text-sm text-zinc-400">{{ error }}</p>
                <button
                    type="button"
                    @click="loadCommands"
                    class="mt-6 rounded-xl bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-violet-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-400"
                >
                    Try again
                </button>
            </div>

            <!-- Empty state -->
            <div
                v-else-if="commands.length === 0"
                class="mx-auto max-w-md rounded-3xl border border-dashed border-zinc-700 bg-zinc-900/40 p-10 text-center"
            >
                <div
                    class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-zinc-800 text-zinc-400"
                >
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="h-6 w-6"
                    >
                        <rect x="3" y="3" width="18" height="18" rx="3" />
                        <path
                            d="M8 8h.01M12 8h.01M16 8h.01M8 12h.01M12 12h.01M8 16h.01"
                        />
                    </svg>
                </div>
                <p class="font-semibold text-white">No commands yet</p>
                <p class="mt-2 text-sm text-zinc-400">
                    Add commands to your deck from the configuration page to see
                    them here.
                </p>
            </div>

            <!-- Commands grid -->
            <div
                v-else
                class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5"
            >
                <button
                    v-for="command in commands"
                    :key="command.code"
                    type="button"
                    :class="
                        cn(
                            'group relative flex aspect-square flex-col items-center justify-center gap-2 overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900/60 p-3 text-center shadow-xl shadow-black/30 transition-all duration-200 hover:scale-[1.03] hover:border-violet-400/50 focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-400',
                            executingCode === command.code &&
                                'scale-95 border-violet-400 bg-violet-600/20',
                        )
                    "
                    @click="execute(command)"
                >
                    <!-- Command image or icon -->
                    <span
                        v-if="imageUrl(command)"
                        class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-xl bg-zinc-800"
                    >
                        <img
                            :src="imageUrl(command)!"
                            :alt="command.description"
                            class="h-full w-full object-cover"
                        />
                    </span>
                    <span
                        v-else
                        class="flex h-16 w-16 items-center justify-center rounded-xl bg-linear-to-br from-zinc-800 to-zinc-900 text-zinc-500 transition-all group-hover:text-violet-400"
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
                            <path
                                d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"
                            />
                            <path d="M13.7 21a2 2 0 0 1-3.4 0" />
                        </svg>
                    </span>

                    <span
                        class="w-full truncate text-sm font-semibold text-white"
                        :title="command.description"
                    >
                        {{ command.description }}
                    </span>
                    <span
                        class="w-full truncate rounded-md bg-black/30 px-2 py-0.5 font-mono text-[10px] text-zinc-500"
                        :title="command.command"
                    >
                        {{ command.command }}
                    </span>
                </button>
            </div>
        </main>
    </div>
</template>
