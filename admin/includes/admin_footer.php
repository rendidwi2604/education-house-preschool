    </div><!-- /content -->
  </div><!-- /main -->
</div><!-- /admin-shell -->

<footer style="margin-left:var(--sidebar-w);background:#fff;border-top:1px solid #E8ECF4;padding:12px 24px;display:flex;align-items:center;justify-content:space-between;font-size:12px;color:#94A3B8;font-weight:600;">
  <span>© <?= date('Y') ?> Education House Preschool — Admin Panel</span>
  <span style="display:flex;align-items:center;gap:4px;">
    <i class="fa-solid fa-heart" style="color:#EC4899;font-size:11px;"></i>
    Dibuat untuk anak-anak Indonesia
  </span>
</footer>

<script>
// Responsive footer margin
(function(){
  var footer = document.querySelector('footer[style*="margin-left"]');
  if(!footer) return;
  if(window.innerWidth <= 768) footer.style.marginLeft = '0';
  window.addEventListener('resize', function(){
    if(!footer) return;
    footer.style.marginLeft = window.innerWidth <= 768 ? '0' : 'var(--sidebar-w)';
  });
})();
</script>
</body>
</html>
