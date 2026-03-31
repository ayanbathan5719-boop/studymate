@if(session('success'))
    <div id="toast-success" style="position: fixed; top: 20px; right: 20px; background: #48bb78; color: white; padding: 15px 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 9999; display: flex; align-items: center; gap: 10px; animation: slideIn 0.3s ease;">
        <span style="font-size: 20px;">✅</span>
        <span>{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" style="background: none; border: none; color: white; font-size: 18px; cursor: pointer; margin-left: 10px;">×</button>
    </div>
@endif

@if(session('error'))
    <div id="toast-error" style="position: fixed; top: 20px; right: 20px; background: #f56565; color: white; padding: 15px 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 9999; display: flex; align-items: center; gap: 10px; animation: slideIn 0.3s ease;">
        <span style="font-size: 20px;">❌</span>
        <span>{{ session('error') }}</span>
        <button onclick="this.parentElement.remove()" style="background: none; border: none; color: white; font-size: 18px; cursor: pointer; margin-left: 10px;">×</button>
    </div>
@endif

<style>
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes fadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }
    
    .toast-hide {
        animation: fadeOut 0.3s ease forwards;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-hide toasts after 5 seconds
        const toasts = document.querySelectorAll('[id^="toast-"]');
        toasts.forEach(toast => {
            setTimeout(() => {
                toast.classList.add('toast-hide');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.remove();
                    }
                }, 300);
            }, 5000);
        });
    });
</script>