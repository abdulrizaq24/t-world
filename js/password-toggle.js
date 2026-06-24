document.addEventListener('click', function (event) {
  const toggle = event.target.closest('[data-password-toggle]');

  if (!toggle) {
    return;
  }

  const field = toggle.closest('.password-field');
  const input = field ? field.querySelector('[data-password-input]') : null;

  if (!input) {
    return;
  }

  const showing = input.type === 'text';
  input.type = showing ? 'password' : 'text';
  toggle.setAttribute('aria-pressed', showing ? 'false' : 'true');
  toggle.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
});
