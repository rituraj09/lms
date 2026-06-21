// Navbar scroll + mobile toggle
const nav = document.querySelector('.nav');
window.addEventListener('scroll', () => nav.classList.toggle('scrolled', window.scrollY > 10));
const burger = document.querySelector('.hamburger');
const links = document.querySelector('.nav-links');
burger?.addEventListener('click', () => links.classList.toggle('open'));

// Scroll reveal animations
const io = new IntersectionObserver((entries) => {
  entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('show'); });
}, { threshold: 0.12 });
document.querySelectorAll('.reveal').forEach(el => io.observe(el));

// Tabs
document.querySelectorAll('[data-tabs]').forEach(group => {
  const tabs = group.querySelectorAll('.tab');
  const panels = group.querySelectorAll('.tab-panel');
  tabs.forEach((tab, i) => tab.addEventListener('click', () => {
    tabs.forEach(t => t.classList.remove('active'));
    panels.forEach(p => p.classList.remove('active'));
    tab.classList.add('active');
    panels[i].classList.add('active');
  }));
});

// Accordion
document.querySelectorAll('.accordion-head').forEach(head => {
  head.addEventListener('click', () => head.parentElement.classList.toggle('open'));
});

// Form validation + success
const form = document.querySelector('#demoForm');
form?.addEventListener('submit', e => {
  e.preventDefault();
  if (form.checkValidity()) {
    form.style.display = 'none';
    document.querySelector('.success-msg').style.display = 'block';
  } else form.reportValidity();
});

// Table filter (Cognitive/Life pages)
document.querySelectorAll('[data-filter]').forEach(btn => {
  btn.addEventListener('click', () => {
    const f = btn.dataset.filter;
    document.querySelectorAll('[data-filter]').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('tbody tr').forEach(row => {
      row.style.display = (f === 'all' || row.dataset.age === f) ? '' : 'none';
    });
  });
});
