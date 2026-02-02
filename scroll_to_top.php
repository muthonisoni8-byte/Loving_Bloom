<style>
    .scroll-top-btn {
        position: fixed;
        bottom: 30px;
        right: 20px;
        z-index: 100; 
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #001f3f;
        color: white;
        border: 2px solid white;
        outline: none;
        cursor: pointer;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        transition: all 0.3s ease;
        opacity: 0;
        visibility: hidden;
        transform: translateY(20px);
    }

    .scroll-top-btn.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .scroll-top-btn:hover {
        background-color: #f39c12;
        transform: translateY(-5px);
        color: white;
    }

    @media (min-width: 768px) {
        .scroll-top-btn { right: 30px; }
    }
</style>

<button id="scrollToTopComponent" class="scroll-top-btn" aria-label="Scroll to top">
    <i class="fas fa-arrow-up"></i>
</button>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const scrollBtn = document.getElementById('scrollToTopComponent');
        
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                scrollBtn.classList.add('show');
            } else {
                scrollBtn.classList.remove('show');
            }
        });
        scrollBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
</script>