// ChatApp front end: polls the server every 2 seconds and sends messages with fetch().
(function () {
  const csrf = document.querySelector('meta[name="csrf"]').content;
  const me = parseInt(document.querySelector('meta[name="me"]').content, 10);
  const box = document.getElementById('messages');
  const contacts = document.getElementById('contacts');
  const titleEl = document.getElementById('title');
  const form = document.getElementById('composer');
  const input = document.getElementById('text');
  const app = document.getElementById('app');

  let current = 0;      // 0 = group room, otherwise the other user's id
  let lastId = 0;       // newest message id already on screen
  let busy = false;

  function timeText(ts) {
    return new Date(ts * 1000).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }

  function addMessage(m) {
    const mine = m.sender_id === me;
    const row = document.createElement('div');
    row.className = 'msg ' + (mine ? 'mine' : 'theirs');
    if (!mine && current === 0) {                 // show sender name in group chat
      const who = document.createElement('div');
      who.className = 'who';
      who.textContent = m.username;
      row.appendChild(who);
    }
    const txt = document.createElement('div');
    txt.className = 'txt';
    txt.textContent = m.body;                     // textContent => no HTML injection
    const t = document.createElement('div');
    t.className = 'time';
    t.textContent = timeText(m.created_at);
    row.appendChild(txt);
    row.appendChild(t);
    box.appendChild(row);
  }

  function scrollDown() { box.scrollTop = box.scrollHeight; }

  async function poll() {
    if (busy) return;
    busy = true;
    const convo = current;
    try {
      const res = await fetch('api/fetch.php?with=' + convo + '&after=' + lastId, { credentials: 'same-origin' });
      if (res.status === 401) { location.href = 'login.php'; return; }
      const data = await res.json();
      if (convo !== current) return;              // user switched chat while waiting
      const nearBottom = box.scrollHeight - box.scrollTop - box.clientHeight < 80;
      data.messages.forEach(function (m) { addMessage(m); lastId = Math.max(lastId, m.id); });
      if (data.messages.length && nearBottom) scrollDown();
    } catch (err) { /* network hiccup: try again on the next tick */ }
    finally { busy = false; }
  }

  async function loadContacts() {
    try {
      const res = await fetch('api/users.php', { credentials: 'same-origin' });
      if (!res.ok) return;
      const data = await res.json();
      contacts.querySelectorAll('li[data-user]').forEach(function (li) { li.remove(); });
      data.users.forEach(function (u) {
        const li = document.createElement('li');
        li.className = 'contact' + (u.id === current ? ' active' : '');
        li.dataset.id = u.id;
        li.dataset.user = '1';
        const av = document.createElement('span');
        av.className = 'avatar' + (u.online ? ' online' : '');
        av.textContent = u.username.charAt(0).toUpperCase();
        const nm = document.createElement('span');
        nm.className = 'cname';
        nm.textContent = u.username;
        li.appendChild(av);
        li.appendChild(nm);
        contacts.appendChild(li);
      });
    } catch (err) { /* ignore */ }
  }

  function openChat(id, name) {
    current = id;
    lastId = 0;
    box.innerHTML = '';
    titleEl.textContent = name;
    contacts.querySelectorAll('.contact').forEach(function (li) {
      li.classList.toggle('active', parseInt(li.dataset.id, 10) === id);
    });
    app.classList.add('show-chat');               // mobile: switch to conversation view
    busy = false;
    poll().then(scrollDown);
    input.focus();
  }

  contacts.addEventListener('click', function (ev) {
    const li = ev.target.closest('.contact');
    if (!li) return;
    openChat(parseInt(li.dataset.id, 10), li.querySelector('.cname').textContent);
  });

  document.getElementById('back').addEventListener('click', function () {
    app.classList.remove('show-chat');
  });

  form.addEventListener('submit', async function (ev) {
    ev.preventDefault();
    const text = input.value.trim();
    if (!text) return;
    input.value = '';
    const body = new URLSearchParams({ csrf: csrf, to: String(current), text: text });
    try {
      const res = await fetch('api/send.php', { method: 'POST', body: body, credentials: 'same-origin' });
      if (res.status === 401) { location.href = 'login.php'; return; }
      if (!res.ok) { input.value = text; return; }   // keep the text so it is not lost
      await poll();
      scrollDown();
    } catch (err) { input.value = text; }
  });

  loadContacts();
  poll().then(scrollDown);
  setInterval(poll, 2000);
  setInterval(loadContacts, 10000);
})();
