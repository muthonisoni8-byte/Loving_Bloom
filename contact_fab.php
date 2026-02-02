<?php
$phoneNumber = "+254700000000"; 
$whatsappNumber = "254700000000";
$whatsappMessage = "Hello! I am interested in booking a safari.";
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .fab-container {
        position: fixed;
        bottom: 30px;
        left: 20px;
        z-index: 100;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
        font-family: 'Poppins', sans-serif;
    }

    .fab-options {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
        opacity: 0;
        transform: translateY(20px);
        pointer-events: none;
        transition: all 0.3s ease-out;
        margin-bottom: 5px;
    }

    .fab-container.active .fab-options {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }

    .fab-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 50px;
        color: white;
        text-decoration: none;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        font-weight: bold;
        font-size: 14px;
        width: 140px;
        transition: transform 0.2s;
    }

    .fab-btn:hover {
        transform: scale(1.05);
        color: white;
    }

    .btn-whatsapp { background-color: #25D366; }
    .btn-whatsapp:hover { background-color: #1da851; }

    .btn-call { background-color: #f39c12; } 
    .btn-call:hover { background-color: #d35400; }

    .fab-toggle {
        position: relative;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #001f3f; 
        color: white;
        border: none;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        transition: all 0.3s ease;
        outline: 2px solid white;
    }

    .fab-container.active .fab-toggle {
        background-color: #f39c12; 
        transform: rotate(90deg);
    }

    .icon-close { display: none; }
    .fab-container.active .icon-dots { display: none; }
    .fab-container.active .icon-close { display: block; }

    .status-indicator {
        position: absolute;
        top: 2px;
        right: 2px;
        display: flex;
        height: 12px;
        width: 12px;
    }
    
    .fab-container.active .status-indicator {
        display: none;
    }

    .ping-animation {
        position: absolute;
        display: inline-flex;
        height: 100%;
        width: 100%;
        border-radius: 50%;
        background-color: #4ade80;
        opacity: 0.75;
        animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;
    }

    .static-dot {
        position: relative;
        display: inline-flex;
        border-radius: 50%;
        height: 12px;
        width: 12px;
        background-color: #22c55e;
        border: 2px solid white;
    }

    @keyframes ping {
        75%, 100% {
            transform: scale(2);
            opacity: 0;
        }
    }

    @media (min-width: 768px) {
        .fab-container { left: 30px; }
        .fab-btn { padding: 10px 20px; font-size: 15px; width: 150px; }
    }
</style>

<div id="contact-fab" class="fab-container">
    
    <div class="fab-options">
        <a href="https://wa.me/<?php echo $whatsappNumber; ?>?text=<?php echo urlencode($whatsappMessage); ?>" 
           target="_blank" 
           class="fab-btn btn-whatsapp">
            <i class="fa-brands fa-whatsapp" style="font-size: 18px;"></i>
            <span>WhatsApp</span>
        </a>

        <a href="tel:<?php echo $phoneNumber; ?>" 
           class="fab-btn btn-call">
            <i class="fa-solid fa-phone" style="font-size: 14px;"></i>
            <span>Call Us</span>
        </a>
    </div>

    <button id="fab-toggle-btn" class="fab-toggle" aria-label="Contact Options">
        <span class="status-indicator">
            <span class="ping-animation"></span>
            <span class="static-dot"></span>
        </span>

        <i class="fa-solid fa-comment-dots icon-dots"></i>
        <i class="fa-solid fa-xmark icon-close"></i>
    </button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fabContainer = document.getElementById('contact-fab');
        const toggleBtn = document.getElementById('fab-toggle-btn');

        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            fabContainer.classList.toggle('active');
        });

        document.addEventListener('click', function(event) {
            const isClickInside = fabContainer.contains(event.target);
            
            if (!isClickInside && fabContainer.classList.contains('active')) {
                fabContainer.classList.remove('active');
            }
        });
    });
</script>