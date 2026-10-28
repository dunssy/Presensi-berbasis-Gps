  <!-- ========================================== -->
    <!-- GLOBAL CONFIRMATION MODAL                  -->
    <!-- ========================================== -->
    <div 
        x-show="confirmModal.open" 
        x-cloak 
        class="fixed inset-0 z-[90] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
        role="dialog"
        aria-modal="true"
    >
        <div 
            @click.outside="confirmModal.open = false"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="w-full max-w-md rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl p-6"
        >
            <div class="flex items-start gap-4">
                <div 
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full"
                    :class="confirmModal.variant === 'danger' ? 'bg-red-100 text-red-600 dark:bg-red-950/80 dark:text-red-400' : 'bg-blue-100 text-blue-600 dark:bg-blue-950/80 dark:text-blue-400'"
                >
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white" x-text="confirmModal.title"></h3>
                    <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400 leading-relaxed" x-text="confirmModal.message"></p>
                </div>
            </div>
            <div class="mt-6 flex items-center justify-end gap-3">
                <button 
                    type="button" 
                    @click="confirmModal.open = false"
                    class="px-4 py-2 text-sm font-medium rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                >
                    Batal
                </button>
                <button 
                    type="button" 
                    @click="executeConfirm()"
                    class="px-4 py-2 text-sm font-semibold rounded-xl text-white transition shadow-xs"
                    :class="confirmModal.variant === 'danger' ? 'bg-red-600 hover:bg-red-700' : 'bg-primary-600 hover:bg-primary-700'"
                    x-text="confirmModal.confirmText"
                ></button>
            </div>
        </div>
    </div>
