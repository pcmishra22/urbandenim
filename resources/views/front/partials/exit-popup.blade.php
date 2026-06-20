{{--
    Exit Intent Popup
    - Fires when mouse leaves browser window (desktop) or after 25s on mobile
    - Shows once per session (cookie-gated)
    - Captures WhatsApp number OR email for follow-up
    - Offers FIRST10 discount code
--}}

@php
    // Don't show on checkout or order confirmation pages
    $currentRoute = request()->route()?->getName() ?? '';
    $hideOnRoutes = ['checkout.index', 'checkout.confirmation', 'checkout.store'];
    $showPopup = !in_array($currentRoute, $hideOnRoutes);
@endphp

@if($showPopup)
<!-- Exit Intent Popup -->
<div id="exit-popup-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:99999;align-items:center;justify-content:center;padding:16px;">
    <div id="exit-popup-box" style="background:#fff;border-radius:18px;max-width:420px;width:100%;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.25);position:relative;animation:popIn .3s ease;">

        {{-- Close button --}}
        <button onclick="closeExitPopup()" style="position:absolute;top:12px;right:14px;background:none;border:none;font-size:1.4rem;color:#999;cursor:pointer;line-height:1;z-index:1;">×</button>

        {{-- Header --}}
        <div style="background:linear-gradient(135deg,#D19C97 0%,#c4756e 100%);padding:28px 28px 20px;text-align:center;">
            <div style="font-size:2rem;margin-bottom:6px;">🎁</div>
            <div style="color:#fff;font-size:1.25rem;font-weight:700;line-height:1.3;">Wait! Before you go...</div>
            <div style="color:rgba(255,255,255,.9);font-size:.88rem;margin-top:6px;">Get <strong>10% OFF</strong> your first Jeanzo order</div>
        </div>

        {{-- Body --}}
        <div style="padding:22px 28px 28px;">

            {{-- Step 1: offer --}}
            <div id="popup-step-1">
                <div style="text-align:center;margin-bottom:18px;">
                    <div style="font-size:.95rem;color:#333;line-height:1.6;">
                        Premium denim, free shipping, 7-day returns.<br>
                        Use code below at checkout:
                    </div>
                    <div style="margin:14px auto;background:#f8f9fa;border:2px dashed #D19C97;border-radius:10px;padding:10px 20px;display:inline-block;">
                        <span style="font-size:1.5rem;font-weight:800;letter-spacing:3px;color:#c4756e;">FIRST10</span>
                    </div>
                    <div style="font-size:.78rem;color:#888;">10% off your first order · Expires in 24 hours</div>
                </div>

                {{-- Capture WhatsApp or email --}}
                <div style="font-size:.85rem;font-weight:600;color:#333;margin-bottom:8px;">
                    Get this code on WhatsApp or Email:
                </div>
                <form id="exit-popup-form" onsubmit="submitExitPopup(event)" style="display:flex;gap:8px;">
                    <input type="text" id="popup-contact" name="contact"
                           placeholder="WhatsApp number or email"
                           style="flex:1;padding:10px 14px;border:1.5px solid #ddd;border-radius:10px;font-size:.9rem;outline:none;"
                           onfocus="this.style.borderColor='#D19C97'" onblur="this.style.borderColor='#ddd'">
                    <button type="submit"
                            style="background:#D19C97;color:#fff;border:none;border-radius:10px;padding:10px 16px;font-weight:600;cursor:pointer;white-space:nowrap;font-size:.88rem;">
                        Send Code
                    </button>
                </form>
                <div style="font-size:.73rem;color:#aaa;margin-top:8px;text-align:center;">
                    No spam. We only send your discount code.
                </div>

                <div style="text-align:center;margin-top:16px;">
                    <a href="{{ route('products.index') }}"
                       onclick="closeExitPopup()"
                       style="display:inline-block;background:#222;color:#fff;padding:11px 28px;border-radius:10px;font-weight:600;font-size:.9rem;text-decoration:none;">
                        Shop Now & Use FIRST10
                    </a>
                </div>

                <div style="text-align:center;margin-top:10px;">
                    <button onclick="closeExitPopup()" style="background:none;border:none;color:#aaa;font-size:.78rem;cursor:pointer;text-decoration:underline;">
                        No thanks, I'll pay full price
                    </button>
                </div>
            </div>

            {{-- Step 2: thank you --}}
            <div id="popup-step-2" style="display:none;text-align:center;padding:10px 0;">
                <div style="font-size:2.5rem;margin-bottom:10px;">✅</div>
                <div style="font-size:1.1rem;font-weight:700;color:#333;margin-bottom:6px;">Code sent!</div>
                <div style="font-size:.88rem;color:#555;margin-bottom:20px;">Use <strong>FIRST10</strong> at checkout for 10% off.</div>
                <a href="{{ route('products.index') }}"
                   onclick="closeExitPopup()"
                   style="display:inline-block;background:#D19C97;color:#fff;padding:11px 28px;border-radius:10px;font-weight:600;font-size:.9rem;text-decoration:none;">
                    Shop Now →
                </a>
            </div>

        </div>
    </div>
</div>

<style>
@keyframes popIn{from{transform:scale(.85);opacity:0}to{transform:scale(1);opacity:1}}
#exit-popup-overlay.show{display:flex!important}
</style>

<script>
(function(){
    // Don't show if already seen this session
    if (sessionStorage.getItem('jz_exit_shown')) return;

    var shown = false;

    function showPopup() {
        if (shown) return;
        shown = true;
        sessionStorage.setItem('jz_exit_shown', '1');
        document.getElementById('exit-popup-overlay').classList.add('show');
    }

    // Desktop: mouse leaves window top
    document.addEventListener('mouseleave', function(e) {
        if (e.clientY <= 10) showPopup();
    });

    // Mobile: show after 25 seconds of being on page
    var mobileTimer = setTimeout(function() {
        if (/Mobi|Android/i.test(navigator.userAgent)) showPopup();
    }, 25000);

    // Also show on back button attempt (popstate)
    window.addEventListener('beforeunload', function() {
        if (!shown) showPopup();
    });

})();

function closeExitPopup() {
    document.getElementById('exit-popup-overlay').classList.remove('show');
}

// Close on overlay click (outside box)
document.getElementById('exit-popup-overlay').addEventListener('click', function(e) {
    if (e.target === this) closeExitPopup();
});

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeExitPopup();
});

function submitExitPopup(e) {
    e.preventDefault();
    var contact = document.getElementById('popup-contact').value.trim();
    if (!contact) return;

    // Save contact via AJAX
    fetch('{{ route("exit.capture") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ contact: contact })
    }).catch(function(){});  // Silently fail — UX must not break

    // Show thank you regardless of server response
    document.getElementById('popup-step-1').style.display = 'none';
    document.getElementById('popup-step-2').style.display = 'block';
}
</script>
@endif
