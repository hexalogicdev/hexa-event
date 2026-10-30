@php
$containerFooter = (isset($configData['contentLayout']) && $configData['contentLayout'] === 'compact') ? 'container-xxl' : 'container-fluid';
@endphp

<footer class="content-footer footer bg-footer-theme">
  <div class="{{ $containerFooter }}">
    <div class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column gap-2">
      <div class="text-body">
        &copy; <script>document.write(new Date().getFullYear())</script> Event Management System
      </div>
      <div class="text-body-secondary small">
        <a href="{{ route('legal.privacy') }}" class="text-body-secondary text-decoration-none">Privacy policy</a>
        <span class="mx-2">&middot;</span>
        <a href="{{ route('legal.terms') }}" class="text-body-secondary text-decoration-none">Terms</a>
        <span class="mx-2">·</span>
        Premium event operations, booking, and check-in workflows.
      </div>
    </div>
  </div>
</footer>
