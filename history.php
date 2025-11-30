<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>My Order - Green Basket</title>
<style>
:root{
  --green-dark:#2E6E3E;
  --green-light:#C9E7C4;
  --green-complete:#2ECC71;
  --yellow-process:#F4C542;
  --muted:#7A7A7A;
  --bg:#F4F4F4;
  --card-radius:22px;
  --shadow:0 2px 8px rgba(0,0,0,0.05);
  --font:"Inter",system-ui,Arial;
}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:var(--font);background:var(--bg);padding:18px}
.app{max-width:960px;margin:auto}

/* HEADER */
header{
  background:var(--green-dark);padding:14px;border-radius:14px;display:flex;align-items:center;gap:12px;
}
.back{width:42px;height:42px;display:flex;align-items:center;justify-content:center;border-radius:10px;background:rgba(255,255,255,0.06);cursor:pointer}
.title{font-size:20px;font-weight:700;color:white}
.search-wrap{margin-left:auto;display:flex;align-items:center;gap:8px}
.search-input{height:38px;padding:6px 12px;border-radius:10px;border:none;min-width:200px}
.search-btn,.clear-btn{height:38px;padding:0 12px;border-radius:10px;color:white;background:transparent;cursor:pointer;border:1px solid rgba(255,255,255,0.2)}

/* TABS */
.tabs{display:flex;gap:20px;padding:16px 4px}
.tab{font-size:18px;font-weight:700;cursor:pointer;color:var(--muted)}
.tab.active{color:var(--green-dark);text-decoration:underline}

/* CARD */
.content{padding-top:8px}
.card{background:white;box-shadow:var(--shadow);border-radius:var(--card-radius);padding:16px;margin-bottom:14px;display:flex;gap:14px;flex-wrap:wrap;align-items:center}
.pimg{width:110px;height:110px;border-radius:14px;overflow:hidden;background:#f6f6f6;flex:0 0 110px}
.pimg img{width:100%;height:100%;object-fit:cover;display:block}
.info{flex:1}
.prod-name{font-size:18px;font-weight:800;margin-bottom:6px}
.price{font-size:15px;color:var(--green-dark);font-weight:700;margin-bottom:4px} /* <-- harga satuan di card */
.meta{color:var(--muted);font-size:13px;margin-bottom:6px;font-style:italic}
.badge{padding:6px 14px;border-radius:999px;font-weight:700;font-size:13px}
.badge.process{background:var(--yellow-process)}
.badge.complete{background:var(--green-complete);color:white}
.actions{display:flex;gap:10px}
.btn{padding:8px 16px;border-radius:999px;font-weight:700;border:2px solid transparent;cursor:pointer;text-decoration:none;text-align:center;display:inline-block}
.btn.outline{border-color:var(--green-dark);color:var(--green-dark);background:white}
.btn.fill{background:var(--green-light);color:var(--green-dark)}

/* EMPTY MESSAGE */
.empty-msg{
  background:white;
  padding:30px;
  border-radius:14px;
  margin-top:20px;
  text-align:center;
  color:var(--muted);
  font-size:16px;
  display:none;
}

/* VIEW DETAILS MODAL */
.modal-backdrop{
  position:fixed;inset:0;background:rgba(0,0,0,0.45);
  display:none;align-items:center;justify-content:center;z-index:2000;
}
.modal{width:94%;max-width:480px;background:white;border-radius:14px;padding:18px}
.modal-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px}
.close-btn{background:none;border:0;font-size:22px;font-weight:bold;cursor:pointer}
.detail-row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #eee}
.detail-row:last-child{border-bottom:0}

/* BUY AGAIN (Add to Basket) MODAL */
.buyback-backdrop{
  position:fixed;inset:0;background:rgba(0,0,0,0.45);display:none;align-items:center;justify-content:center;z-index:2100;
}
.buyback-modal{
  width:92%;max-width:420px;background:white;border-radius:12px;padding:16px;
}
.buyback-top{display:flex;gap:12px;align-items:center;margin-bottom:10px}
.buyback-top .thumb{width:84px;height:84px;border-radius:10px;overflow:hidden;background:#f6f6f6}
.buyback-top .thumb img{width:100%;height:100%;object-fit:cover}
.buyback-info{flex:1}
.qty-control{display:flex;align-items:center;gap:10px;margin-top:12px}
.qty-btn{width:36px;height:36px;border-radius:8px;border:1px solid #ddd;background:white;cursor:pointer;font-weight:700}
.qty-value{min-width:36px;text-align:center;font-weight:700}

/* PRICE ROW */
.price-row{margin-top:10px;display:flex;justify-content:space-between;align-items:center;gap:12px;padding-top:8px;border-top:1px solid #f1f1f1}
.price-row .label{color:#666;font-size:13px}
.price-row .value{font-weight:800;color:var(--green-dark)}

/* RESPONSIVE */
@media(max-width:600px){
  .card{flex-direction:column;align-items:flex-start}
  .pimg{width:100%;height:160px}
  .buyback-top{flex-direction:column;align-items:flex-start}
  .buyback-top .thumb{width:100%;height:180px}
}
</style>
</head>
<body>

<div class="app">

  <!-- HEADER -->
  <header>
    <div class="back" onclick="window.location.href='Home.php'">
      <svg width="18" height="18" viewBox="0 0 24 24" stroke="white" fill="none" stroke-width="2">
        <path d="M15 18L9 12L15 6" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>

    <div class="title">My Order</div>

    <div class="search-wrap">
      <input id="searchInput" class="search-input" placeholder="Cari...">
      <button id="searchBtn" class="search-btn">Search</button>
      <button id="clearBtn" class="clear-btn">Clear</button>
    </div>
  </header>

  <!-- TABS -->
  <div class="tabs">
    <div class="tab active" data-target="process">Process</div>
    <div class="tab" data-target="complete">Complete</div>
  </div>

  <!-- PROCESS (card price = satuan) -->
  <div id="process" class="content">

    <!-- harga satuan (number) -->
        <div class="card"
        data-id="ORD001"
        data-name="Eggs"
        data-price="6000"
        data-qty="10"
        data-status="Process"
        data-title="eggs telur"
        data-note="Sedang diproses oleh toko"
        data-img="https://i.ibb.co/8gWcLT8/eggs.png">


      <div class="pimg"><img src="https://i.ibb.co/8gWcLT8/eggs.png" alt="Eggs"></div>

      <div class="info">
        <div class="prod-name">Eggs</div>
        <!-- Harga satuan tampil di card -->
        <div class="price">Rp 6.000</div>
        <div class="meta">Sedang diproses oleh toko</div>
        <span class="badge process">On Process</span>
      </div>

      <div class="actions">
        <button class="btn outline view-details">View Details</button>
      </div>
    </div>

    <div class="card"
      data-id="ORD002"
      data-name="Broccoli Fresh"
      data-price="12000"
      data-qty="1"
      data-status="Process"
      data-title="broccoli fresh hijau"
      data-note="Sedang diproses oleh toko"
      data-img="https://i.ibb.co/njBwHLS/broccoli.png">

      <div class="pimg"><img src="https://i.ibb.co/njBwHLS/broccoli.png" alt="Broccoli Fresh"></div>

      <div class="info">
        <div class="prod-name">Broccoli Fresh</div>
        <!-- Harga satuan tampil di card -->
        <div class="price">Rp 12.000</div>
        <div class="meta">Sedang diproses oleh toko</div>
        <span class="badge process">On Process</span>
      </div>

      <div class="actions">
        <button class="btn outline view-details">View Details</button>
      </div>
    </div>

    <div id="emptyProcess" class="empty-msg">Kamu belum memiliki pesanan yang sedang diproses.</div>
  </div>

  <!-- COMPLETE (card price = satuan) -->
  <div id="complete" class="content" style="display:none">

    <div class="card"
      data-id="ORD050"
      data-name="Broccoli"
      data-price="12000"
      data-qty="2"
      data-status="Complete"
      data-title="broccoli selesai"
      data-note="Diterima 2 hari lalu"
      data-img="https://i.ibb.co/njBwHLS/broccoli.png">

      <div class="pimg"><img src="https://i.ibb.co/njBwHLS/broccoli.png" alt="Broccoli"></div>

      <div class="info">
        <div class="prod-name">Broccoli</div>
        <!-- Harga satuan tampil di card -->
        <div class="price">Rp 12.000</div>
        <div class="meta">Diterima 2 hari lalu</div>
        <span class="badge complete">Complete</span>
      </div>

      <div class="actions">
        <button class="btn fill buy-again">Buy Again</button>
        <button class="btn outline view-details">View Details</button>
      </div>
    </div>

    <div id="emptyComplete" class="empty-msg">Kamu belum memiliki pesanan yang selesai.</div>
  </div>

</div>

<!-- VIEW DETAILS MODAL: menampilkan HARGA = satuan * qty -->
<div id="modalBackdrop" class="modal-backdrop" aria-hidden="true">
  <div class="modal" role="dialog" aria-modal="true">
    <div class="modal-header">
      <div class="modal-title">Order Details</div>
      <button class="close-btn" id="modalClose" aria-label="Close">&times;</button>
    </div>
    <div>
      <div class="detail-row"><div>ID Order</div><div id="d-id"></div></div>
      <div class="detail-row"><div>Produk</div><div id="d-name"></div></div>
      <div class="detail-row"><div>Qty</div><div id="d-qty"></div></div>
      <!-- di sini HANYA tampil total = unit * qty -->
      <div class="detail-row"><div>Harga</div><div id="d-price"></div></div>
      <div class="detail-row"><div>Status</div><div id="d-status"></div></div>
      <div class="detail-row"><div>Catatan</div><div id="d-note"></div></div>
    </div>
  </div>
</div>

<!-- BUY AGAIN MODAL: tampilkan unit + total (user bisa adjust qty) -->
<div id="buybackBackdrop" class="buyback-backdrop" aria-hidden="true">
  <div class="buyback-modal" role="dialog" aria-modal="true" aria-label="Buy again">
    <div class="buyback-top">
      <div class="thumb"><img id="bb-image" src="" alt="Product"></div>
      <div class="buyback-info">
        <div id="bb-name" style="font-weight:800;margin-bottom:6px;"></div>

        <div class="price-row">
          <div class="label">Harga / unit</div>
          <div id="bb-unit" class="value">Rp 0</div>
        </div>

        <div class="qty-control" style="margin-top:8px;">
          <button id="bb-minus" class="qty-btn">−</button>
          <div id="bb-qty" class="qty-value">1</div>
          <button id="bb-plus" class="qty-btn">+</button>
        </div>

        <div class="price-row" style="margin-top:12px;">
          <div class="label">Total</div>
          <div id="bb-total" class="value">Rp 0</div>
        </div>
      </div>
    </div>

    <div style="margin-top:14px;display:flex;gap:10px;">
      <button id="bb-add" class="btn fill" style="flex:1">Add to Basket</button>
      <button id="bb-cancel" class="btn outline" style="flex:0 0 auto">Cancel</button>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){

  /* Tabs */
  const tabs = document.querySelectorAll('.tab');
  let activeTab = 'process';
  tabs.forEach(t=>{
    t.addEventListener('click', ()=>{
      tabs.forEach(x=>x.classList.remove('active'));
      t.classList.add('active');
      activeTab = t.dataset.target;
      document.getElementById('process').style.display = activeTab === 'process' ? 'block' : 'none';
      document.getElementById('complete').style.display = activeTab === 'complete' ? 'block' : 'none';
      document.getElementById('searchInput').value = '';
      resetCards(); updateEmptyState();
    });
  });

  /* Search (debounced) */
  const searchInput = document.getElementById('searchInput');
  let tm;
  searchInput.addEventListener('input', ()=>{ clearTimeout(tm); tm = setTimeout(applySearch, 120); });
  document.getElementById('clearBtn').addEventListener('click', ()=>{ searchInput.value=''; resetCards(); updateEmptyState(); });

  function applySearch(){
    const q = (searchInput.value||'').toLowerCase().trim();
    const box = document.getElementById(activeTab);
    const cards = box.querySelectorAll('.card');
    let shown = 0;
    cards.forEach(card=>{
      const combined = ((card.dataset.title||'') + ' ' + (card.dataset.name||'')).toLowerCase();
      const match = combined.includes(q);
      card.style.display = match ? 'flex' : 'none';
      if(match) shown++;
    });
    updateEmptyState();
    if(q !== '' && shown === 0){
      if(activeTab === 'process') document.getElementById('emptyProcess').style.display = 'block';
      else document.getElementById('emptyComplete').style.display = 'block';
    }
  }

  function resetCards(){ ['process','complete'].forEach(id=>document.querySelectorAll(`#${id} .card`).forEach(c=>c.style.display='flex')); }
  function updateEmptyState(){
    const processCount = [...document.querySelectorAll('#process .card')].filter(c=>c.style.display !== 'none').length;
    const completeCount = [...document.querySelectorAll('#complete .card')].filter(c=>c.style.display !== 'none').length;
    document.getElementById('emptyProcess').style.display = processCount === 0 ? 'block' : 'none';
    document.getElementById('emptyComplete').style.display = completeCount === 0 ? 'block' : 'none';
  }
  updateEmptyState();

  /* View Details: show Harga = satuan * qty */
  const modalBackdrop = document.getElementById('modalBackdrop');
  const modalClose = document.getElementById('modalClose');

  document.body.addEventListener('click', function(e){
    const vd = e.target.closest('.view-details');
    if(vd){
      const card = vd.closest('.card');
      // id & name
      const id = card.dataset.id || '';
      const name = card.dataset.name || card.querySelector('.prod-name')?.textContent || '';
      // qty (prefer data-qty)
      let qty = Number(card.dataset.qty || 0);
      if(!qty){
        const metaText = card.querySelector('.meta')?.textContent || '';
        const found = metaText.match(/(\d+)/);
        qty = found ? Number(found[1]) : 0;
      }
      // unit price (prefer data-price)
      let unit = Number(card.dataset.price || 0);
      if(!unit){
        const priceText = card.querySelector('.price')?.textContent || '';
        const digits = priceText.replace(/[^\d]/g,'');
        unit = digits ? Number(digits) : 0;
      }
      const total = unit * (qty || 0);

      const status = card.dataset.status || '';
      const noteFromData = (card.dataset.note || '').toString().trim();
      const note = noteFromData !== '' ? noteFromData : (status.toLowerCase().includes('process') ? 'Sedang diproses oleh toko' : 'Tidak ada catatan');

      document.getElementById('d-id').textContent = id;
      document.getElementById('d-name').textContent = name;
      document.getElementById('d-qty').textContent = (qty || '-');
      // *** tampilkan HANYA total = unit * qty di modal View Details ***
      document.getElementById('d-price').textContent = 'Rp ' + (total).toLocaleString('id-ID');
      document.getElementById('d-status').textContent = status;
      document.getElementById('d-note').textContent = note;

      modalBackdrop.style.display = 'flex';
    }
    if(e.target === modalBackdrop) modalBackdrop.style.display = 'none';
  });

  modalClose.addEventListener('click', ()=> modalBackdrop.style.display = 'none');

  /* Buy Again modal: unit + total, update on qty */
  const buybackBackdrop = document.getElementById('buybackBackdrop');
  const bbImage = document.getElementById('bb-image');
  const bbName = document.getElementById('bb-name');
  const bbUnit = document.getElementById('bb-unit');
  const bbTotal = document.getElementById('bb-total');
  const bbQtyEl = document.getElementById('bb-qty');

  let currentBuy = { id:null, price:0, qty:1 };

  document.body.addEventListener('click', function(e){
    const ba = e.target.closest('.buy-again');
    if(ba){
      const card = ba.closest('.card');
      currentBuy.id = card.dataset.id;
      currentBuy.price = Number(card.dataset.price) || 0;
      currentBuy.qty = 1;
      bbImage.src = card.dataset.img || '';
      bbName.textContent = card.dataset.name || '';
      bbUnit.textContent = 'Rp ' + (currentBuy.price).toLocaleString('id-ID');
      bbQtyEl.textContent = currentBuy.qty;
      bbTotal.textContent = 'Rp ' + (currentBuy.price * currentBuy.qty).toLocaleString('id-ID');
      buybackBackdrop.style.display = 'flex';
    }
  });

  document.getElementById('bb-plus').addEventListener('click', ()=>{
    currentBuy.qty++;
    bbQtyEl.textContent = currentBuy.qty;
    bbTotal.textContent = 'Rp ' + (currentBuy.price * currentBuy.qty).toLocaleString('id-ID');
  });

  document.getElementById('bb-minus').addEventListener('click', ()=>{
    if(currentBuy.qty > 1){
      currentBuy.qty--;
      bbQtyEl.textContent = currentBuy.qty;
      bbTotal.textContent = 'Rp ' + (currentBuy.price * currentBuy.qty).toLocaleString('id-ID');
    }
  });

  document.getElementById('bb-add').addEventListener('click', ()=>{
    window.location.href = `Basket.php?add=${encodeURIComponent(currentBuy.id)}&qty=${encodeURIComponent(currentBuy.qty)}`;
  });

  document.getElementById('bb-cancel').addEventListener('click', ()=> buybackBackdrop.style.display='none');
  buybackBackdrop.addEventListener('click', (e)=> { if(e.target === buybackBackdrop) buybackBackdrop.style.display='none'; });

  // close modals with ESC
  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape'){
      if(modalBackdrop.style.display === 'flex') modalBackdrop.style.display = 'none';
      if(buybackBackdrop.style.display === 'flex') buybackBackdrop.style.display = 'none';
    }
  });

});
</script>

</body>
</html>
