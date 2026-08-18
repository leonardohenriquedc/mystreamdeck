<script setup lang="ts">
import { router } from '@inertiajs/vue3';

function goToDeck() {
    router.visit('/deck');
}

function goToConfiguration() {
    router.visit('/configuration');
}
</script>

<template>
    <div
        class="relative min-h-screen overflow-hidden bg-zinc-950 text-zinc-100"
    >
        <!-- Decorative background glows -->
        <div aria-hidden="true" class="pointer-events-none absolute inset-0">
            <div
                class="absolute -top-32 left-1/2 h-96 w-2xl -translate-x-1/2 rounded-full bg-violet-600/20 blur-3xl"
            ></div>
            <div
                class="absolute right-0 bottom-0 h-80 w-80 rounded-full bg-cyan-500/10 blur-3xl"
            ></div>
            <div
                class="absolute bottom-10 left-0 h-72 w-72 rounded-full bg-fuchsia-600/10 blur-3xl"
            ></div>
        </div>

        <main
            class="relative mx-auto flex min-h-screen w-full max-w-6xl flex-col items-center justify-center gap-16 px-6 py-16"
        >
            <!-- Brand -->
            <header class="flex flex-col items-center text-center">
                <div
                    class="mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-linear-to-br from-violet-500 to-fuchsia-600 shadow-lg shadow-violet-900/40"
                >
                    <!-- Stream deck grid icon -->
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        class="h-8 w-8 text-white"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <rect x="3" y="3" width="18" height="18" rx="3" />
                        <path
                            d="M8 8h.01M12 8h.01M16 8h.01M8 12h.01M12 12h.01M8 16h.01"
                        />
                        <path d="M16 12.5v3.5M16 16h.01" />
                    </svg>
                </div>

                <p
                    class="mb-3 text-sm font-semibold tracking-[0.3em] text-violet-400 uppercase"
                >
                    Control deck
                </p>
                <h1
                    class="text-4xl font-bold tracking-tight text-white sm:text-5xl"
                >
                    Welcome to MyStreamDeck
                </h1>
                <p class="mt-4 max-w-xl text-balance text-zinc-400">
                    Trigger your shell commands from a sleek, modern dashboard.
                    Pick up where you left off and launch your deck, or head
                    into configuration to shape it to your workflow.
                </p>
            </header>

            <!-- Launcher buttons -->
            <nav class="grid w-full max-w-2xl grid-cols-1 gap-5 sm:grid-cols-2">
                <button
                    type="button"
                    @click="goToDeck"
                    class="group relative flex flex-col items-start gap-4 overflow-hidden rounded-3xl border border-violet-500/30 bg-linear-to-br from-violet-600/20 to-fuchsia-600/10 p-7 text-left shadow-xl shadow-violet-950/30 transition-all duration-300 hover:scale-[1.02] hover:border-violet-400/60 hover:shadow-violet-800/30 focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-400"
                >
                    <span
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-violet-500 text-white shadow-lg shadow-violet-900/40 transition-transform duration-300 group-hover:rotate-6"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="h-6 w-6"
                        >
                            <rect x="3" y="3" width="18" height="18" rx="3" />
                            <path
                                d="M8 8h.01M12 8h.01M16 8h.01M8 12h.01M12 12h.01M8 16h.01"
                            />
                            <path d="M16 12.5v3.5M16 16h.01" />
                        </svg>
                    </span>
                    <span>
                        <span class="block text-xl font-semibold text-white"
                            >Open Deck</span
                        >
                        <span
                            class="mt-1 block text-sm leading-relaxed text-zinc-400"
                        >
                            Launch your stream deck and fire off your commands
                            with a single click.
                        </span>
                    </span>
                    <span
                        class="absolute top-7 right-5 text-violet-400 transition-transform duration-300 group-hover:translate-x-1"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="h-5 w-5"
                        >
                            <path d="M5 12h14" />
                            <path d="m13 6 6 6-6 6" />
                        </svg>
                    </span>
                </button>

                <button
                    type="button"
                    @click="goToConfiguration"
                    class="group relative flex flex-col items-start gap-4 overflow-hidden rounded-3xl border border-zinc-800 bg-zinc-900/60 p-7 text-left shadow-xl shadow-black/30 transition-all duration-300 hover:scale-[1.02] hover:border-zinc-600 hover:bg-zinc-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400"
                >
                    <span
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-zinc-700 text-zinc-100 shadow-lg shadow-black/40 transition-transform duration-300 group-hover:rotate-90"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="h-6 w-6"
                        >
                            <path
                                d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"
                            />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </span>
                    <span>
                        <span class="block text-xl font-semibold text-white"
                            >Configuration</span
                        >
                        <span
                            class="mt-1 block text-sm leading-relaxed text-zinc-400"
                        >
                            Set up your commands and deck layout to fit your
                            exact needs.
                        </span>
                    </span>
                    <span
                        class="absolute top-7 right-5 text-zinc-400 transition-transform duration-300 group-hover:translate-x-1"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="h-5 w-5"
                        >
                            <path d="M5 12h14" />
                            <path d="m13 6 6 6-6 6" />
                        </svg>
                    </span>
                </button>
            </nav>

            <footer class="mt-4 text-center text-xs text-zinc-600">
                Runs locally · MyStreamDeck
            </footer>
        </main>
    </div>
</template>
