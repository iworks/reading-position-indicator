document.addEventListener('DOMContentLoaded', function() {
    var css_class = '';

    if (typeof iworks_position !== 'undefined') {
        if (iworks_position.style === 'gradient') {
            css_class = 'multiple';
        } else if (iworks_position.style === 'transparent') {
            css_class = 'single';
        }
        if (css_class) {
            css_class += ' ';
        }
        css_class += iworks_position.style;
        css_class += ' position-' + iworks_position.position;
    }

    // Create and append progress element
    var progress = document.createElement('progress');
    progress.value = 0;
    progress.id = 'reading-position-indicator';
    progress.className = css_class || '';
    progress.innerHTML = '<div class="progress-container"><span class="progress-bar"></span></div>';
    document.body.appendChild(progress);

    var getMax = function() {
        var end = document.querySelector('.reading-position-indicator-end');
        if (end) {
            var endRect = end.getBoundingClientRect();
            return endRect.top + window.pageYOffset - (window.innerHeight * 0.75);
        }
        return document.documentElement.scrollHeight - window.innerHeight;
    };

    var getValue = function() {
        return window.pageYOffset || document.documentElement.scrollTop;
    };

    var updateProgress = function() {
        var value = getValue();
        if ('value' in progress) {
            progress.value = value;
        } else {
            var progressBar = document.querySelector('.progress-bar');
            if (progressBar) {
                var max = getMax();
                var width = max > 0 ? (value / max) * 100 : 0;
                progressBar.style.width = width + '%';
            }
        }
    };

    var handleResize = function() {
        var max = getMax();
        if ('max' in progress) {
            progress.max = max;
        }
        updateProgress();
    };

    // Check if progress element supports the value property
    if ('max' in document.createElement('progress')) {
        progress.max = getMax();
        document.addEventListener('scroll', updateProgress);
    } else {
        updateProgress(); // Initial update for non-progress browsers
    }

    // Event listeners
    window.addEventListener('load', function() {
        updateProgress();
    });

    window.addEventListener('resize', handleResize);
    window.addEventListener('orientationchange', handleResize);
});