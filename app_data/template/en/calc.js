// Validates with [jshint]
// The playground site includes JSHint and provides linting as you enter code.
//
// Uncomment the line below to see what happens:
// Error Test

/* jshint strict: true */
(function() {
    'use strict';

    function setup() {
        var valueX = document.getElementById('value-x');
        var op = document.querySelector('select');
        var valueY = document.getElementById('value-y');
        var btn = document.querySelector('button');
        
        btn.onclick = function() {
            var data = {
                x: valueX.value,
                op: op.value,
                y: valueY.value,
            };

            var url = 'api/calculate';
            fetch(url, {
                method: 'POST',
                cache: 'no-store',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (!data.success) {
                    throw data.error;
                }
                showCalcResult(data.result);
                op.selectedIndex = getRandomInt(4);
                valueX.value = getRandomInt(1000000);
                valueY.value = getRandomInt(1000000);
            })
            .catch(function(error) {
                showCalcResult('Error: ' + error);
            });            
        };
    }

    function getRandomInt(max) {
        return Math.floor(Math.random() * Math.floor(max));
    }

    function showCalcResult(text) {
        var section = document.querySelector('.calc-result');
        var ul = section.querySelector('ul');
        var item = document.createElement('li');
        item.textContent = text;
        ul.insertAdjacentElement('afterbegin', item);
        section.style.display = '';
    }

    function loadPolyfill() {
        var url = 'https://polyfill.io/v3/polyfill.min.js?features=fetch';
        var script = document.createElement('script');
        script.onload = function() { setup(); };
        script.onerror = function() {
            showCalcResult('Error loading Script: ' + url);
            document.querySelector('.calc-result').style.backgroundColor = 'red';
        };
        script.src = url;
        document.head.appendChild(script);        
    }

    // Once content is loaded run [setup] or if using IE or an
    // Older Mobile Device download a polyfill for [fetch, Promise, etc].
    document.addEventListener('DOMContentLoaded', function() {
        if (window.fetch === undefined) {
            loadPolyfill();
        } else {
            setup();
        }
    });
})();
