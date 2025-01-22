jQuery(document).ready(function($) {
    const mainBalloon = $('.main-balloon');
    const additionalBalloons = $('.additional-balloons-container');
    const tooltip = $('.balloon-tooltip');
    const scrollToTop = $('.scroll-to-top');
    let isOpen = false;
    let tooltipTimer = null;
    let tooltipShowTimer = null;

    // Função para mostrar/esconder balões adicionais
    function toggleAdditionalBalloons(e) {
        if (e) {
            e.preventDefault();
        }
        
        if (isOpen) {
            additionalBalloons.removeClass('active');
            mainBalloon.removeClass('active');
        } else {
            additionalBalloons.addClass('active');
            mainBalloon.addClass('active');
        }
        isOpen = !isOpen;
    }

    // Evento de clique no balão principal
    mainBalloon.on('click', toggleAdditionalBalloons);

    // Gerenciamento do Tooltip
    if (typeof balloonTooltipConfig !== 'undefined' && balloonTooltipConfig.active) {
        function showTooltip() {
            clearTimeout(tooltipShowTimer);
            clearTimeout(tooltipTimer);
            
            tooltipShowTimer = setTimeout(() => {
                tooltip.text(balloonTooltipConfig.text);
                tooltip.addClass('active');
                
                // Esconder após o tempo definido
                tooltipTimer = setTimeout(() => {
                    hideTooltip();
                }, balloonTooltipConfig.duration);
            }, balloonTooltipConfig.delayShow);
        }

        function hideTooltip() {
            clearTimeout(tooltipShowTimer);
            clearTimeout(tooltipTimer);
            tooltip.removeClass('active');
        }

        // Mostrar tooltip apenas na primeira visita
        if (!localStorage.getItem('tooltipShown')) {
            showTooltip();
            localStorage.setItem('tooltipShown', 'true');
            
            // Resetar após 24 horas
            setTimeout(() => {
                localStorage.removeItem('tooltipShown');
            }, 24 * 60 * 60 * 1000);
        }

        // Mostrar tooltip ao passar o mouse (após a primeira vez)
        mainBalloon.hover(
            showTooltip,
            hideTooltip
        );
    }

    // Gerenciamento da seta para cima
    function toggleScrollToTop() {
        if ($(window).scrollTop() > 300) {
            scrollToTop.addClass('active');
        } else {
            scrollToTop.removeClass('active');
        }
    }

    // Verificar posição da rolagem ao carregar e ao rolar
    toggleScrollToTop();
    $(window).on('scroll', toggleScrollToTop);

    // Evento de clique na seta para cima
    scrollToTop.on('click', function() {
        $('html, body').animate({
            scrollTop: 0
        }, 800);
    });

    // Fechar balões ao clicar fora
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#floating-balloons').length && isOpen) {
            toggleAdditionalBalloons();
        }
    });

    // Tooltip functionality
    if (tooltip.length) {
        var delayShow = parseInt(tooltip.data('delay-show'));
        var delayHide = parseInt(tooltip.data('delay-hide'));
        var showTimeout;

        // Show tooltip after delay
        setTimeout(function() {
            tooltip.addClass('show');
        }, delayShow);

        // Hide tooltip after showing
        setTimeout(function() {
            tooltip.removeClass('show');
        }, delayShow + 5000); // Show for 5 seconds

        // Handle hover
        $('.main-balloon').hover(
            function() {
                clearTimeout(showTimeout);
                tooltip.addClass('show');
            },
            function() {
                showTimeout = setTimeout(function() {
                    tooltip.removeClass('show');
                }, delayHide);
            }
        );
    }
});
