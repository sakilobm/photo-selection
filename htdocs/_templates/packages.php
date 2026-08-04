<?php use Aether\Session; ?>

<!-- ══════ HERO HEADER ══════ -->
<section class="relative pt-36 pb-16 px-6 max-w-7xl mx-auto text-center space-y-6" data-reveal>
  <span class="badge badge-gold">Official Investment Guide 2026</span>
  <h1 class="text-4xl md:text-6xl font-extrabold text-white font-['Outfit'] tracking-tight mt-3">
    Transparent <span class="grad-gold">Wedding &amp; Production Tiers</span>
  </h1>
  <p class="text-slate-400 text-base md:text-lg max-w-3xl mx-auto font-light leading-relaxed">
    Invest in memories that last generations. All rates are in <strong class="text-amber-400">Indian Rupees (₹ INR)</strong> with zero hidden charges. Choose a signature tier or customize your team.
  </p>
</section>

<!-- ══════ PACKAGES GRID ══════ -->
<section class="pt-20 pb-12 px-6 md:px-12 max-w-7xl mx-auto">
  <div id="pricing-packages-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 items-start">
    <?php foreach ($packages as $p): 
        $formattedPrice = '₹' . number_format($p['price'], 0, '.', ',');
        
        $cardClass = 'glass-card p-8 flex flex-col justify-between relative group';
        $cardStyle = '';
        $badgeClass = 'badge';
        $checkmarkColorClass = 'text-slate-400';
        $priceColorClass = 'text-white';
        $checkIcon = 'check';
        $btnClass = 'btn-ghost w-full justify-center mt-8 text-xs py-3';
        $topBadgeHtml = '';

        if ($p['id'] === 'silver') {
            $badgeClass = 'badge';
        } else if ($p['id'] === 'gold') {
            $cardClass = 'glass-card p-8 flex flex-col justify-between relative scale-105 z-10 border-amber-500/40';
            $cardStyle = 'background-image:linear-gradient(rgba(30,15,0,0.6),rgba(7,13,24,0.85)),linear-gradient(135deg,rgba(255,183,3,0.5) 0%,rgba(167,139,250,0.2) 50%,rgba(255,183,3,0.4) 100%); box-shadow: 0 0 60px rgba(255,183,3,0.2);';
            $badgeClass = 'badge badge-gold';
            $checkmarkColorClass = 'text-amber-400';
            $priceColorClass = 'text-amber-400';
            $checkIcon = 'check-circle';
            $btnClass = 'btn-primary btn-gold w-full justify-center mt-8 text-xs py-3.5';
            $topBadgeHtml = '
              <div class="absolute -top-4 left-1/2 -translate-x-1/2 badge badge-gold shadow-lg whitespace-nowrap flex items-center gap-1.5">
                <i data-lucide="flame" class="w-3.5 h-3.5 text-amber-400"></i> Most Popular
              </div>
            ';
        } else if ($p['id'] === 'platinum') {
            $badgeClass = 'badge badge-cyan';
            $checkmarkColorClass = 'text-[var(--theme-accent)]';
            $priceColorClass = 'grad-cyan';
            $btnClass = 'btn-primary w-full justify-center mt-8 text-xs py-3';
        } else if ($p['id'] === 'imperial') {
            $badgeClass = 'badge badge-purple';
            $checkmarkColorClass = 'text-purple-400';
            $priceColorClass = 'text-purple-400';
            $btnClass = 'btn-ghost w-full justify-center mt-8 text-xs py-3';
        }

        $durationTag = $p['id'] === 'silver' ? 'All inclusive • Per Event Day' :
                      ($p['id'] === 'gold' ? 'All inclusive • 2 Event Days' :
                      ($p['id'] === 'platinum' ? 'All inclusive • 3 Event Days' :
                      'All inclusive • Full Stage Crew'));
    ?>
      <div class="<?= $cardClass ?>" style="<?= $cardStyle ?>" data-reveal>
        <?= $topBadgeHtml ?>
        <div class="space-y-6 <?= $p['id'] === 'gold' ? 'pt-2' : '' ?>">
          <div class="space-y-2">
            <span class="<?= $badgeClass ?>"><?= htmlspecialchars($p['badge'] ?? 'Official Tier') ?></span>
            <h3 class="text-2xl font-bold text-white font-['Outfit']"><?= htmlspecialchars($p['name']) ?></h3>
            <p class="text-xs text-slate-400"><?= htmlspecialchars($p['desc'] ?? '') ?></p>
          </div>
          <div class="py-4 border-y border-slate-800 <?= $p['id'] === 'gold' ? 'border-amber-500/30' : '' ?>">
            <div class="text-3xl font-extrabold <?= $priceColorClass ?> font-['Outfit']" <?= $p['id'] === 'platinum' ? 'style="-webkit-text-fill-color:var(--theme-accent)"' : '' ?>><?= $formattedPrice ?></div>
            <span class="text-[11px] text-slate-400 <?= $p['id'] === 'gold' ? 'text-amber-200/60' : '' ?>"><?= $durationTag ?></span>
          </div>
          <ul class="space-y-3 text-xs text-slate-300 text-left">
            <?php foreach ($p['features'] as $feat): ?>
              <li class="flex items-center gap-2.5">
                <i data-lucide="<?= $checkIcon ?>" class="w-4 h-4 <?= $checkmarkColorClass ?> flex-shrink-0"></i>
                <span><?= htmlspecialchars($feat) ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <button onclick="selectPackage('<?= htmlspecialchars(addslashes($p['name'])) ?>', <?= $p['price'] ?>)" class="<?= $btnClass ?>">
          Select <?= htmlspecialchars($p['name']) ?>
        </button>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- ══════ CUSTOM CALCULATOR ══════ -->
<section class="py-16 px-6 md:px-12 max-w-5xl mx-auto" data-reveal>
  <div class="glass-card p-8 md:p-12 space-y-8" style="border-color:rgba(255,183,3,0.3)">
    <div class="text-center space-y-3">
      <span class="badge badge-gold">Interactive Rate Calculator</span>
      <h2 class="text-2xl md:text-4xl font-bold text-white font-['Outfit']">Build Your Custom Package</h2>
      <p class="text-slate-400 text-sm">Select options to calculate your custom estimate in Indian Rupees (₹ INR).</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
      <label class="flex items-center gap-3 p-4 rounded-xl bg-slate-900/60 border border-slate-800 cursor-pointer hover:border-amber-400/60 transition-colors">
        <input type="checkbox" id="opt-drone" class="w-4 h-4 accent-amber-500" onchange="calculateCustomRate()">
        <div><span class="font-bold text-white block">4K Drone Aerial Coverage</span><span class="text-xs text-amber-400 font-mono">+ ₹20,000 / Day</span></div>
      </label>
      <label class="flex items-center gap-3 p-4 rounded-xl bg-slate-900/60 border border-slate-800 cursor-pointer hover:border-amber-400/60 transition-colors">
        <input type="checkbox" id="opt-led" class="w-4 h-4 accent-amber-500" onchange="calculateCustomRate()">
        <div><span class="font-bold text-white block">LED Wall Stage Video Display</span><span class="text-xs text-amber-400 font-mono">+ ₹45,000 / Event</span></div>
      </label>
      <label class="flex items-center gap-3 p-4 rounded-xl bg-slate-900/60 border border-slate-800 cursor-pointer hover:border-amber-400/60 transition-colors">
        <input type="checkbox" id="opt-prewedding" class="w-4 h-4 accent-amber-500" onchange="calculateCustomRate()">
        <div><span class="font-bold text-white block">Outdoor Pre-Wedding Film Shoot</span><span class="text-xs text-amber-400 font-mono">+ ₹35,000</span></div>
      </label>
      <label class="flex items-center gap-3 p-4 rounded-xl bg-slate-900/60 border border-slate-800 cursor-pointer hover:border-amber-400/60 transition-colors">
        <input type="checkbox" id="opt-live" class="w-4 h-4 accent-amber-500" onchange="calculateCustomRate()">
        <div><span class="font-bold text-white block">YouTube &amp; Web Live Streaming</span><span class="text-xs text-amber-400 font-mono">+ ₹25,000</span></div>
      </label>
    </div>
    <div class="p-6 rounded-2xl bg-gradient-to-r from-amber-950/40 via-slate-900 to-slate-900 border border-amber-500/40 flex flex-col sm:flex-row items-center justify-between gap-4">
      <div>
        <span class="text-xs uppercase tracking-wider text-amber-300 font-semibold">Estimated Custom Investment:</span>
        <div id="custom-total-price" class="text-3xl font-extrabold text-amber-400 font-['Outfit']">₹65,000</div>
      </div>
      <button onclick="bookCustomEstimate()" class="btn-primary btn-gold text-xs py-3 px-6">
        <i data-lucide="send" class="w-4 h-4"></i> Request Custom Proposal
      </button>
    </div>
  </div>
</section>

<!-- ══════ TOAST DEMO PLAYGROUND ══════ -->
<section class="py-16 px-6 md:px-12 max-w-5xl mx-auto" data-reveal>
  <div class="glass-card p-8 md:p-12 space-y-6" style="border-color:rgba(var(--theme-accentRGB),0.3)">
    <div class="space-y-2">
      <span class="badge badge-cyan">Notification System Demo</span>
      <h2 class="text-2xl md:text-3xl font-bold text-white font-['Outfit']">Toast Notification Playground</h2>
      <p class="text-slate-400 text-xs md:text-sm">Test our notification system — customize color, position and duration in real-time.</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
      <div>
        <label class="block font-semibold text-slate-300 mb-1">Toast Theme</label>
        <select id="toast-demo-type" class="w-full p-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white">
          <option value="gold">Luxury Gold</option>
          <option value="sapphire">Sapphire Cyan</option>
          <option value="success">Emerald Success</option>
          <option value="warning">Amber Warning</option>
          <option value="error">Rose Error</option>
          <option value="purple">Imperial Purple</option>
        </select>
      </div>
      <div>
        <label class="block font-semibold text-slate-300 mb-1">Position</label>
        <select id="toast-demo-pos" class="w-full p-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white">
          <option value="bottom-right">Bottom Right</option>
          <option value="bottom-left">Bottom Left</option>
          <option value="bottom-center">Bottom Center</option>
          <option value="top-right">Top Right</option>
          <option value="top-left">Top Left</option>
          <option value="top-center">Top Center</option>
        </select>
      </div>
      <div>
        <label class="block font-semibold text-slate-300 mb-1">Duration (ms)</label>
        <input type="number" id="toast-demo-duration" value="4000" class="w-full p-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono">
      </div>
    </div>
    <div class="flex flex-wrap items-center gap-4">
      <button onclick="triggerDemoToast()" class="btn-primary text-xs py-3 px-6">
        <i data-lucide="bell-ring" class="w-4 h-4"></i> Trigger Custom Toast
      </button>
      <button onclick="showToast('OBM Studio', 'Package updated — ₹ INR pricing confirmed!', 'gold')" class="btn-ghost text-xs py-3 px-6">
        Quick Gold Toast
      </button>
    </div>
  </div>
</section>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) lucide.createIcons();

    // Scroll reveal
    const ro = new IntersectionObserver((entries) => {
      entries.forEach((e, i) => {
        if (e.isIntersecting) {
          setTimeout(() => e.target.classList.add('revealed'), i * 80);
          ro.unobserve(e.target);
        }
      });
    }, { threshold: 0.1 });
    document.querySelectorAll('[data-reveal]').forEach(el => ro.observe(el));
  });

  function selectPackage(name, price) {
    const formatted = new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', maximumFractionDigits: 0 }).format(price);
    showToast(`${name} Selected!`, `Package at ${formatted} added to your quote. Team will contact you.`, 'gold', { duration: 5000, position: 'bottom-center' });
  }

  function calculateCustomRate() {
    let base = 65000;
    if (document.getElementById('opt-drone').checked) base += 20000;
    if (document.getElementById('opt-led').checked) base += 45000;
    if (document.getElementById('opt-prewedding').checked) base += 35000;
    if (document.getElementById('opt-live').checked) base += 25000;
    const f = new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', maximumFractionDigits: 0 }).format(base);
    document.getElementById('custom-total-price').textContent = f;
  }

  function bookCustomEstimate() {
    const p = document.getElementById('custom-total-price').textContent;
    showToast('Custom Estimate Saved!', `Your package total is ${p}. We'll contact you within 24 hours!`, 'success', { position: 'bottom-center' });
  }

  function triggerDemoToast() {
    const type = document.getElementById('toast-demo-type').value;
    const pos = document.getElementById('toast-demo-pos').value;
    const dur = parseInt(document.getElementById('toast-demo-duration').value) || 4000;
    showToast(`${type.charAt(0).toUpperCase() + type.slice(1)} Notification`, `Showing at ${pos} for ${dur}ms — OBM Toast Engine`, type, { position: pos, duration: dur });
  }
</script>
