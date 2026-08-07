<?php use Aether\Session; ?>

<!-- ══════ FOOTER ══════ -->
<footer class="py-14 px-6 border-t border-slate-800/60 bg-slate-950/90 text-slate-400 text-xs relative z-10">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-cyan-400 via-purple-500 to-amber-400 flex items-center justify-center">
                <i data-lucide="camera" class="w-4 h-4 text-slate-950"></i>
            </div>
            <div>
                <span class="font-black text-sm text-white">OBM STUDIO</span>
                <span class="block text-[10px] text-slate-500">Photography & Cinema Since 2014</span>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-6">
            <a href="<?= Session::url('index') ?>#story" class="hover:text-white transition-colors">Our Story</a>
            <a href="<?= Session::url('index') ?>#services" class="hover:text-white transition-colors">Services</a>
            <a href="<?= Session::url('packages') ?>" class="hover:text-amber-400 transition-colors">Packages (₹)</a>
            <a href="<?= Session::url('digital-album') ?>" class="hover:text-white transition-colors">Digital Album</a>
            <a href="<?= Session::url('photo-selection') ?>" class="text-cyan-400 hover:underline">Client Portal</a>
        </div>
        <p>© 2026 OBM Studio — Crafted with ❤️ by their sons.</p>
    </div>
</footer>

<!-- ══════ CLIENT LOGIN MODAL ══════ -->
<div id="login-modal" class="modal-overlay">
    <div class="modal-box relative">
        <button id="close-login-btn" class="absolute top-4 right-4 text-slate-400 hover:text-white transition-colors">
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>
        <div class="space-y-5">
            <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 border border-cyan-400/40 flex items-center justify-center text-cyan-400">
                <i data-lucide="lock" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-2xl font-black text-white font-['Outfit']">Client Photo Selection</h3>
                <p class="text-xs text-slate-400 mt-2 leading-relaxed">Enter your registered email and passcode to access your private high-resolution photo gallery and make your album selections.</p>
            </div>
            <form id="client-login-form" class="space-y-4">
                <div>
                    <label for="client-email" class="block text-xs font-bold uppercase text-slate-300 mb-2 tracking-wider">Email Address</label>
                    <input type="email" id="client-email" required placeholder="e.g. vikram@example.com" class="w-full px-4 py-3.5 rounded-xl bg-slate-900/90 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-400 transition-colors text-sm">
                </div>
                <div>
                    <label for="event-passcode" class="block text-xs font-bold uppercase text-slate-300 mb-2 tracking-wider">Event Passcode</label>
                    <input type="text" id="event-passcode" required placeholder="e.g. DEMO2026" class="w-full px-4 py-3.5 rounded-xl bg-slate-900/90 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-400 transition-colors uppercase font-mono tracking-widest text-sm">
                </div>
                <div id="passcode-error" class="hidden text-xs text-rose-400 bg-rose-500/10 border border-rose-500/20 rounded-lg p-2.5"></div>
                <button type="submit" class="btn-primary w-full justify-center py-4">
                    <span>Unlock Photo Portal</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>
            <div class="pt-4 border-t border-slate-800 flex items-center justify-between text-xs">
                <span class="text-slate-400">Testing? Use:</span>
                <button id="demo-code-btn" type="button" class="text-cyan-400 hover:underline font-bold font-mono">Auto-fill DEMO2026</button>
            </div>
        </div>
    </div>
</div>
