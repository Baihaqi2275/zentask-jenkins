<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>ZENTASK - Calendar</title>

  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.4/index.global.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

  <style>
    :root{
      --bg-1: #f6fbff;
      --bg-2: #ffffff;
      --card: #ffffff;
      --muted: #667085;
      --accent: #06b6d4; /* cyan */
      --accent-2: #0ea5e9; /* sky */
      --text: #0f172a;
      --soft: rgba(2,6,23,0.06);
      --radius: 12px;
    }
    [data-theme="dark"]{
      --bg-1:#041423; --bg-2:#061427; --card:#071126; --muted:#9fb3c9; --text:#e6eefb; --soft: rgba(255,255,255,0.04);
    }

    html,body{height:100%;margin:0;font-family:'Poppins',Inter,system-ui,-apple-system,"Segoe UI",Roboto,Arial;background:linear-gradient(180deg,var(--bg-1),var(--bg-2));color:var(--text);-webkit-font-smoothing:antialiased}
    .container{max-width:1200px;margin:22px auto;padding:18px}

    /* top bar layout: left/back+brand, center search, right actions */
    header {
      display: grid;
      grid-template-columns: 1fr auto 1fr;
      align-items: center;
      gap: 12px;
      margin-bottom: 18px;
    }
    .left {
      display:flex;align-items:center;gap:12px;
    }
    .back-btn {
      display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:12px;background:linear-gradient(180deg,rgba(255,255,255,0.85),rgba(255,255,255,0.75));color:var(--text);text-decoration:none;border:1px solid var(--soft);box-shadow:0 6px 18px var(--soft);font-weight:600;
    }
    [data-theme="dark"] .back-btn { background:transparent;border:1px solid rgba(255,255,255,0.04) }
    .brand { display:flex;align-items:center;gap:12px }
    .brand .logo { width:44px;height:44px;border-radius:10px;background:linear-gradient(135deg,var(--accent),var(--accent-2));color:white;display:flex;align-items:center;justify-content:center;font-weight:800;box-shadow:0 8px 24px rgba(6,165,175,0.12) }
    .brand .title { display:flex;flex-direction:column;line-height:1 }
    .brand .name { font-weight:700 }
    .brand .sub { font-size:12px;color:var(--muted) }

    /* center search */
    .center { display:flex;justify-content:center; }
    .search-wrap { width:540px; max-width:82%; display:flex;gap:8px;align-items:center;background:rgba(15,23,42,0.03);border-radius:999px;padding:8px 12px;border:1px solid var(--soft) }
    .search-wrap input { flex:1;border:0;background:transparent;padding:8px 12px;border-radius:999px;font-size:14px }
    .search-wrap button { background:transparent;border:0;padding:8px 10px;border-radius:8px;color:var(--muted);cursor:pointer }

    /* right actions */
    .right { display:flex;justify-content:flex-end;gap:10px;align-items:center }
    .action-btn { padding:10px 14px;border-radius:12px;border:0;cursor:pointer;font-weight:600;display:inline-flex;align-items:center;gap:10px }
    .btn-voice { background:linear-gradient(90deg,#fff,#fff); color:#334155;border:1px solid var(--soft); box-shadow:0 6px 18px var(--soft); padding:9px 12px;border-radius:14px }
    .btn-add { background:linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; box-shadow:0 10px 30px rgba(14,165,233,0.16); border-radius:14px;padding:10px 14px }

    /* calendar card */
    .card{background:var(--card);border-radius:16px;padding:18px;box-shadow:0 12px 40px var(--soft);border:1px solid var(--soft);overflow:hidden}
    #calendar { height:720px; border-radius:10px; overflow:hidden; background:transparent; padding:8px }

    /* fullcalendar button tweaks to match design */
    .fc .fc-toolbar-title { font-weight:700; font-size:20px; }
    .fc .fc-button {
      border-radius:12px; padding:8px 12px; border:0; box-shadow:none; font-weight:700;
      background:transparent; color:var(--text); border:1px solid transparent;
    }
    .fc .fc-button-primary { background:linear-gradient(90deg,var(--accent),var(--accent-2)); color:white; box-shadow:0 8px 20px rgba(6,165,175,0.12) }
    .fc .fc-daygrid-day-frame, .fc .fc-timegrid-event, .fc td { border-color: rgba(15,23,42,0.04) }

    /* modal as right-side panel like screenshot */
    .modal-overlay { display:none; position:fixed; inset:0; z-index:9990; background:rgba(2,6,23,0.18); backdrop-filter: blur(6px); align-items:flex-start; justify-content:flex-end; padding:28px; }
    .modal-card { width:420px; max-width:94%; border-radius:14px; padding:20px; background:var(--card); box-shadow:0 30px 80px rgba(2,6,23,0.12); border:1px solid var(--soft); overflow:auto }
    .modal-card h3 { margin:0 0 6px 0; font-size:20px; }
    .modal-close { position:absolute; right:32px; top:30px; background:transparent;border:0;font-size:20px;color:var(--muted);cursor:pointer }

    .form-row{ display:flex; gap:10px; align-items:center; margin-top:12px; }
    .form-label{ display:flex; align-items:center; gap:8px; font-weight:700; color:var(--muted); font-size:13px; margin-bottom:6px }
    .modal-card input, .modal-card textarea, .modal-card select { width:100%; box-sizing:border-box; font-size:14px; padding:12px; border-radius:10px; border:1px solid var(--soft); background:transparent; color:var(--text) }
    .modal-card textarea { min-height:110px; resize:vertical }

    /* color palette */
    .color-palette { display:flex; gap:8px; align-items:center; margin-top:8px; flex-wrap:wrap }
    .color-swatch { width:36px;height:36px;border-radius:8px;border:2px solid transparent;cursor:pointer;box-shadow:0 6px 18px rgba(2,6,23,0.06) }
    .color-swatch.active { outline:3px solid rgba(2,6,23,0.06) }

    .modal-actions { display:flex; justify-content:flex-end; gap:8px; margin-top:18px }
    .btn-ghost { background:transparent;border:1px solid var(--soft); padding:8px 12px;border-radius:10px; cursor:pointer }
    .btn-danger { background:#ef4444;color:white;padding:8px 12px;border-radius:10px;border:0 }

    /* vn modal re-use styles */
    #vnModal .modal-card { width:380px }
    #transcriptBox { padding:12px;border-radius:10px;border:1px dashed var(--soft);min-height:84px;background:transparent; color:var(--muted) }

    /* responsive */
    @media(max-width:880px){
      header{grid-template-columns:1fr;row-gap:10px}
      .center{order:3}
      .right{order:2;justify-content:flex-start}
      .left{order:1}
      #calendar{height:620px}
      .modal-overlay{padding:18px; align-items:center; justify-content:center}
      .modal-card{width:94%}
    }
  </style>
</head>
<body data-theme="light" class="page-fade">
  <div class="container">
    <header>
      <div class="left">
        <a id="backBtn" class="back-btn" href="{{ route('dashboard') }}" data-back-url="{{ route('dashboard') }}" aria-label="Back to dashboard">
          <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="18" height="18">
            <path d="M3 11.5L12 4l9 7.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M5 11v6a2 2 0 002 2h10a2 2 0 002-2v-6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <span class="text">Back</span>
        </a>

        <div class="brand" style="margin-left:6px">
          <div class="logo">ZT</div>
          <div class="title">
            <div class="name">ZENTASK</div>
            <div class="sub">Smart Calendar</div>
          </div>
        </div>
      </div>

      <div class="center">
        <div class="search-wrap" role="search">
          <input id="searchBox" placeholder="Search events..." aria-label="Search events">
          <button title="Voice note" id="btnRecord" class="btn-voice" aria-label="Voice Note">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 1.5a2.75 2.75 0 00-2.75 2.75v5.5A2.75 2.75 0 0012 12.5a2.75 2.75 0 002.75-2.75v-5.5A2.75 2.75 0 0012 1.5z" stroke="#0f172a" stroke-width="1.2"/></svg>
            <span style="margin-left:6px; font-weight:700">Voice Note</span>
          </button>
        </div>
      </div>

      <div class="right">
        <button id="btnExport" class="action-btn" title="Export ICS" style="background:transparent;border:1px solid var(--soft);border-radius:12px;padding:8px 10px">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 3v12" stroke="#0f172a" stroke-width="1.4" stroke-linecap="round"/><path d="M8 11l4 4 4-4" stroke="#0f172a" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>

        <label class="small muted" style="margin-right:6px">Dark</label>
        <input type="checkbox" id="toggleDark" style="transform:scale(1.03)">

        <button id="btnNew" class="btn-add">+ Add Event</button>
      </div>
    </header>

    <div class="card">
      <div id="calendar"></div>
    </div>

    <!-- Add Event modal (right panel) -->
    <div id="modal" class="modal-overlay" style="display:none">
      <div class="modal-card" role="dialog" aria-modal="true">
        <h3 id="modalTitle">Add Event</h3>
        <button id="closeBtn" class="modal-close" title="Close">&times;</button>

        <div style="margin-top:12px">
          <div class="form-label"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" style="opacity:.9"><path d="M6 2v2M18 2v2M3 8h18" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg> Title</div>
          <input id="evTitle" placeholder="Event title">
        </div>

        <div style="margin-top:12px">
          <div class="form-label"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" style="opacity:.9"><path d="M4 7h16M4 12h10M4 17h16" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg> Description</div>
          <textarea id="evDesc" placeholder="Add details..."></textarea>
        </div>

        <div class="form-row">
          <div style="flex:1">
            <div class="form-label"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M7 10h10M7 6h10M7 14h4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg> Start</div>
            <input id="evStart" type="datetime-local">
          </div>
          <div style="flex:1">
            <div class="form-label">End</div>
            <input id="evEnd" type="datetime-local">
          </div>
        </div>

        <div class="form-row" style="align-items:flex-start">
          <div style="flex:1">
            <div class="form-label">Category</div>
            <select id="evCategory">
              <option value="">Category (optional)</option>
              @foreach(\App\Models\Category::all() as $cat)
                <option value="{{ $cat->id }}" data-color="{{ $cat->color }}">{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>

          <div style="width:160px">
            <div class="form-label">Color</div>
            <input id="evColor" placeholder="#2dd4bf">
            <div class="color-palette" id="colorPalette" aria-hidden="false" style="margin-top:8px">
              <button type="button" class="color-swatch" data-color="#2dd4bf" style="background:#2dd4bf"></button>
              <button type="button" class="color-swatch" data-color="#3b82f6" style="background:#3b82f6"></button>
              <button type="button" class="color-swatch" data-color="#7c3aed" style="background:#7c3aed"></button>
              <button type="button" class="color-swatch" data-color="#ef4444" style="background:#ef4444"></button>
              <button type="button" class="color-swatch" data-color="#f59e0b" style="background:#f59e0b"></button>
              <button type="button" class="color-swatch" data-color="#06b6d4" style="background:#06b6d4"></button>
            </div>
          </div>
        </div>

        <div style="margin-top:12px">
          <div class="form-label">Recurring</div>
          <select id="evRRule">
            <option value="">No repeat</option>
            <option value="FREQ=DAILY;INTERVAL=1">Daily</option>
            <option value="FREQ=WEEKLY;INTERVAL=1">Weekly</option>
            <option value="FREQ=MONTHLY;INTERVAL=1">Monthly</option>
          </select>
        </div>

        <div class="modal-actions">
          <button id="deleteBtn" class="btn-danger" style="display:none">Delete</button>
          <button id="cancelBtn" class="btn-ghost" onclick="closeModal()" type="button">Cancel</button>
          <button id="saveBtn" class="btn-add" type="button">Save</button>
        </div>
      </div>
    </div>

    <!-- VN modal -->
    <div id="vnModal" class="modal-overlay" style="display:none">
      <div class="modal-card">
        <h3>Record Voice Note</h3>
        <p class="small muted">Speak your reminder. You can transcribe into text to create event automatically.</p>

        <div style="display:flex;gap:8px;align-items:center;margin-bottom:10px">
          <button id="recStart" class="btn-danger" style="padding:8px 12px">Start</button>
          <button id="recStop" style="display:none;padding:8px;border-radius:8px;border:0">Stop</button>
          <div id="recStatus" class="small muted">idle</div>
        </div>

        <div id="transcriptBox" style="min-height:60px;margin-bottom:8px"></div>

        <div style="display:flex;justify-content:flex-end;gap:8px">
          <button id="vnClose" class="btn-ghost" style="background:transparent;padding:8px;border-radius:8px;border:1px solid var(--soft)">Close</button>
          <button id="vnCreate" class="btn-add">Create Event from Transcript</button>
        </div>
      </div>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.4/index.global.min.js"></script>

  <script>
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // elements
    const modal = document.getElementById('modal');
    const vnModal = document.getElementById('vnModal');
    const btnNew = document.getElementById('btnNew');
    const btnExport = document.getElementById('btnExport');
    const searchBox = document.getElementById('searchBox');
    const filterCategory = document.getElementById('filterCategory');
    const toggleDark = document.getElementById('toggleDark');
    const backBtn = document.getElementById('backBtn');

    // modal fields
    const evTitle = document.getElementById('evTitle');
    const evDesc = document.getElementById('evDesc');
    const evStart = document.getElementById('evStart');
    const evEnd = document.getElementById('evEnd');
    const evColor = document.getElementById('evColor');
    const evCategory = document.getElementById('evCategory');
    const evRRule = document.getElementById('evRRule');
    const modalTitle = document.getElementById('modalTitle');
    const saveBtn = document.getElementById('saveBtn');
    const closeBtn = document.getElementById('closeBtn');
    const deleteBtn = document.getElementById('deleteBtn');

    // VN elements
    const btnRecord = document.getElementById('btnRecord');
    const recStart = document.getElementById('recStart');
    const recStop = document.getElementById('recStop');
    const recStatus = document.getElementById('recStatus');
    const transcriptBox = document.getElementById('transcriptBox');
    const vnClose = document.getElementById('vnClose');
    const vnCreate = document.getElementById('vnCreate');

    const importFile = document.getElementById('importFile');
    const btnImport = document.getElementById('btnImport');

    let currentEventId = null;

    // fullcalendar init (kept original config)
    const calendarEl = document.getElementById('calendar');
    let calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'timeGridWeek',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      selectable: true,
      editable: true,
      navLinks: true,
      events: fetchEvents,
      select: (info) => {
        openModal('new', {start: info.startStr, end: info.endStr});
      },
      eventClick: (info) => {
        openModal('edit', {
          id: info.event.id,
          title: info.event.title,
          start: info.event.start ? info.event.start.toISOString() : null,
          end: info.event.end ? info.event.end.toISOString() : null,
          color: info.event.backgroundColor || '',
          extendedProps: info.event.extendedProps || {}
        });
      },
      eventDrop: async (info) => {
        await sendUpdate({id: info.event.id, start: info.event.start? info.event.start.toISOString():null, end: info.event.end? info.event.end.toISOString():null});
      },
      eventResize: async (info) => {
        await sendUpdate({id: info.event.id, start: info.event.start? info.event.start.toISOString():null, end: info.event.end? info.event.end.toISOString():null});
      }
    });
    calendar.render();

    // fetch wrapper
    function fetchEvents(fetchInfo, successCallback, failureCallback){
      const params = new URLSearchParams();
      params.set('start', fetchInfo.startStr);
      params.set('end', fetchInfo.endStr);
      if (searchBox.value.trim()) params.set('search', searchBox.value.trim());
      if (filterCategory && filterCategory.value) params.set('category', filterCategory.value);

      fetch('/events?' + params.toString())
        .then(r=>r.json()).then(events=>{
          successCallback(events);
        }).catch(err=> failureCallback(err));
    }

    // open/close modal (right panel)
    function openModal(mode='new', info={}){
      vnModal.style.display = 'none';
      modal.style.display = 'none';
      window.requestAnimationFrame(() => {
        if(mode==='new'){
          modalTitle.textContent='Add Event';
          evTitle.value = info.title||'';
          evDesc.value = info.extendedProps?.description||'';
          evStart.value = info.start ? toLocalDatetimeInput(info.start) : '';
          evEnd.value = info.end ? toLocalDatetimeInput(info.end) : '';
          evColor.value = info.color||'';
          evCategory.value = info.extendedProps?.category_id || '';
          evRRule.value = info.extendedProps?.rrule || '';
          deleteBtn.style.display='none';
          currentEventId = null;
        } else {
          modalTitle.textContent='Edit Event';
          evTitle.value = info.title||'';
          evDesc.value = info.extendedProps?.description||'';
          evStart.value = info.start ? toLocalDatetimeInput(info.start) : '';
          evEnd.value = info.end ? toLocalDatetimeInput(info.end) : '';
          evColor.value = info.color||'';
          evCategory.value = info.extendedProps?.category_id || '';
          evRRule.value = info.extendedProps?.rrule || '';
          deleteBtn.style.display='inline-block';
          currentEventId = info.id;
        }
        modal.style.display = 'flex';
        setTimeout(()=> { evTitle.focus(); }, 150);
      });
    }
    function closeModal(){ modal.style.display='none'; }

    // save/delete (unchanged behavior)
    saveBtn.addEventListener('click', async ()=>{
      const payload = {
        title: evTitle.value.trim(),
        description: evDesc.value.trim(),
        start: evStart.value ? new Date(evStart.value).toISOString() : null,
        end: evEnd.value ? new Date(evEnd.value).toISOString() : null,
        color: evColor.value || null,
        category_id: evCategory.value || null,
        rrule: evRRule.value || null,
        allDay: false
      };
      if(!payload.title || !payload.start){ alert('Title and start required'); return; }
      try {
        if(currentEventId){
          await fetch('/events/'+currentEventId, { method:'PUT', headers:{ 'Content-Type':'application/json','X-CSRF-TOKEN':csrf }, body: JSON.stringify(payload)});
        } else {
          await fetch('/events', { method:'POST', headers:{ 'Content-Type':'application/json','X-CSRF-TOKEN':csrf }, body: JSON.stringify(payload)});
        }
        calendar.refetchEvents();
        closeModal();
      } catch(err){ alert('Save failed'); console.error(err); }
    });

    deleteBtn.addEventListener('click', async ()=>{
      if(!currentEventId) return;
      if(!confirm('Delete?')) return;
      await fetch('/events/'+currentEventId, { method:'DELETE', headers:{ 'X-CSRF-TOKEN': csrf }});
      calendar.refetchEvents();
      closeModal();
    });

    // update after drag/resize
    async function sendUpdate(payload){
      try {
        await fetch('/events/' + payload.id, {
          method:'PUT',
          headers:{ 'Content-Type':'application/json','X-CSRF-TOKEN': csrf },
          body: JSON.stringify({ start: payload.start, end: payload.end })
        });
        calendar.refetchEvents();
      } catch(err){ console.error(err); }
    }

    // search & filter hooks
    searchBox.addEventListener('input', debounce(()=>calendar.refetchEvents(), 450));
    if (filterCategory) filterCategory.addEventListener('change', ()=>calendar.refetchEvents());

    // export
    btnExport.addEventListener('click', ()=>{ location.href = '/export/ics'; });

    // import handling (kept)
    if(btnImport){
      btnImport.addEventListener('click', async ()=>{
        const f = importFile.files[0];
        if(!f){ alert('Choose a file'); return; }
        const fd = new FormData();
        fd.append('file', f);
        try {
          const res = await fetch('/import', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf },
            body: fd
          });
          const json = await res.json();
          alert('Imported: ' + json.imported);
          calendar.refetchEvents();
        } catch (err) {
          alert('Import failed');
          console.error(err);
        }
      });
    }

    // theme toggle
    const theme = localStorage.getItem('zentask_theme') || 'light';
    document.body.setAttribute('data-theme', theme);
    toggleDark.checked = (theme === 'dark');
    toggleDark.addEventListener('change', (e)=>{
      const t = e.target.checked ? 'dark' : 'light';
      document.body.setAttribute('data-theme', t);
      localStorage.setItem('zentask_theme', t);
    });

    // Back button smooth
    backBtn.addEventListener('click', function(ev){
      if (ev.metaKey || ev.ctrlKey || ev.shiftKey || ev.altKey) return;
      ev.preventDefault();
      const url = backBtn.dataset.backUrl || backBtn.getAttribute('href') || '/';
      document.body.classList.add('fade-out');
      setTimeout(()=> { window.location.href = url; }, 280);
    });

    // Recorder (kept)
    let mediaRecorder, recordedChunks = [];
    btnRecord.addEventListener('click', ()=>{ vnModal.style.display='flex'; });
    recStart.addEventListener('click', async ()=>{
      if(!navigator.mediaDevices) { alert('No microphone'); return; }
      const stream = await navigator.mediaDevices.getUserMedia({ audio:true });
      mediaRecorder = new MediaRecorder(stream);
      recordedChunks = [];
      mediaRecorder.ondataavailable = e=>{ if(e.data.size>0) recordedChunks.push(e.data); };
      mediaRecorder.onstop = async ()=>{
        recStatus.textContent = 'Uploading...';
        const blob = new Blob(recordedChunks, { type:'audio/webm' });
        const fd = new FormData();
        fd.append('audio', blob, 'voice.webm');
        try {
          const res = await fetch('/transcribe', { method:'POST', headers:{ 'X-CSRF-TOKEN': csrf }, body: fd });
          const json = await res.json();
          transcriptBox.innerText = json.transcript || (json.message || 'Check back later');
          if(json.transcript){
            try {
              const p = await fetch('/parse-transcript', {
                method: 'POST',
                headers: {'Content-Type':'application/json','X-CSRF-TOKEN':csrf},
                body: JSON.stringify({ transcript: json.transcript })
              });
              if (p.ok) {
                const pr = await p.json();
                transcriptBox.innerHTML += '<div class="small muted" style="margin-top:8px">Suggested time: ' + (pr.start || 'none') + '</div>';
              }
            } catch(e){}
          }
        } catch(err){
          transcriptBox.innerText = 'Transcription failed';
          console.error(err);
        } finally {
          recStatus.textContent = 'idle';
        }
      };
      mediaRecorder.start();
      recStatus.textContent = 'recording...';
      recStart.style.display='none'; recStop.style.display='inline-block';
    });

    recStop.addEventListener('click', ()=>{
      if(mediaRecorder && mediaRecorder.state!=='inactive') mediaRecorder.stop();
      recStart.style.display='inline-block'; recStop.style.display='none';
    });

    vnClose.addEventListener('click', ()=>{ vnModal.style.display='none'; transcriptBox.innerText=''; });

    // Create from transcript (kept)
    vnCreate.addEventListener('click', async () => {
      const txt = transcriptBox.innerText.trim();
      if (!txt) { alert('No transcript'); return; }

      vnModal.style.display = 'none';
      recStatus.textContent = 'idle';

      let suggested = null;
      try {
        const res = await fetch('/ai-parse', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
          body: JSON.stringify({ transcript: txt })
        });
        if (res.ok) {
          const json = await res.json();
          suggested = json.result || null;
        }
      } catch (err) {
      }

      setTimeout(() => {
        const info = {
          title: (suggested && suggested.title) ? suggested.title : txt,
          start: (suggested && suggested.start) ? suggested.start : new Date().toISOString(),
          end: (suggested && suggested.end) ? suggested.end : null,
          extendedProps: { description: txt, rrule: suggested?.rrule || null }
        };
        openModal('new', info);
      }, 150);
    });

    // small helpers
    function toLocalDatetimeInput(isoStr){
      if(!isoStr) return '';
      const d = new Date(isoStr);
      const yyyy = d.getFullYear();
      const mm = String(d.getMonth()+1).padStart(2,'0');
      const dd = String(d.getDate()).padStart(2,'0');
      const hh = String(d.getHours()).padStart(2,'0');
      const min = String(d.getMinutes()).padStart(2,'0');
      return `${yyyy}-${mm}-${dd}T${hh}:${min}`;
    }
    function debounce(fn, ms=300){ let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a), ms); }; }

    // color palette interaction (set evColor)
    document.querySelectorAll('.color-swatch').forEach(btn=>{
      btn.addEventListener('click', (e)=>{
        document.querySelectorAll('.color-swatch').forEach(b=>b.classList.remove('active'));
        btn.classList.add('active');
        const c = btn.dataset.color;
        evColor.value = c;
      });
    });

    // wire close button inside modal (top-right)
    if(closeBtn){
      closeBtn.addEventListener('click', closeModal);
    }
    // btnNew opens panel
    btnNew.addEventListener('click', ()=> openModal('new', {}));
  </script>
</body>
</html>
