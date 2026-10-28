 <!-- ========================================== -->
    <!-- TOAST NOTIFICATION CONTAINER               -->
    <!-- ========================================== -->
    <div aria-live="polite" class="fixed top-4 right-4 z-[100] flex flex-col gap-2.5 w-full max-w-sm pointer-events-none px-4 sm:px-0">
        <template x-for="toast in toasts" :key="toast.id">
            <div 
                x-show="toast.visible"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                class="pointer-events-auto flex items-start gap-3 p-4 rounded-xl shadow-lg border backdrop-blur-md"
                :class="{
                    'bg-white/95 dark:bg-slate-900/95 border-emerald-200 dark:border-emerald-800/60 text-slate-800 dark:text-slate-100': toast.type === 'success',
                    'bg-white/95 dark:bg-slate-900/95 border-red-200 dark:border-red-800/60 text-slate-800 dark:text-slate-100': toast.type === 'error',
                    'bg-white/95 dark:bg-slate-900/95 border-amber-200 dark:border-amber-800/60 text-slate-800 dark:text-slate-100': toast.type === 'warning',
                    'bg-white/95 dark:bg-slate-900/95 border-blue-200 dark:border-blue-800/60 text-slate-800 dark:text-slate-100': toast.type === 'info'
                }"
                role="alert"
            >
                <div class="shrink-0 mt-0.5">
                    <template x-if="toast.type === 'success'">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400">✓</span>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-red-100 dark:bg-red-950 text-red-600 dark:text-red-400">!</span>
                    </template>
                    <template x-if="toast.type === 'warning'">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-950 text-amber-600 dark:text-amber-400">⚠</span>
                    </template>
                    <template x-if="toast.type === 'info'">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-950 text-blue-600 dark:text-blue-400">i</span>
                    </template>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold" x-text="toast.title"></p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed" x-text="toast.message"></p>
                </div>
                <button @click="dismissToast(toast.id)" class="shrink-0 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-lg">
                    ✕
                </button>
            </div>
        </template>
    </div>
