import '../css/app.css';

// Inicializar Flatpickr y Select2 después de que el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    initializeDatePickers();
    initializeSelect2();
    
    // Reinicializar después de actualizaciones de Livewire
    if (typeof Livewire !== 'undefined') {
        Livewire.hook('morph.updated', ({ el }) => {
            initializeDatePickers(el);
            initializeSelect2(el);
        });
    }
});

function initializeDatePickers(container = document) {
    const dateInputs = container.querySelectorAll('.date-picker');
    dateInputs.forEach(function(input) {
        if (input._flatpickr) {
            return; // Ya está inicializado
        }
        
        if (typeof flatpickr !== 'undefined') {
            flatpickr(input, {
                locale: flatpickr.l10ns.es,
                dateFormat: 'd-m-Y',
                allowInput: true,
                clickOpens: true,
                animate: true,
            });
        }
    });
}

function initializeSelect2(container = document) {
    const selects = container.querySelectorAll('select.select2');
    selects.forEach(function(select) {
        if ($(select).hasClass('select2-hidden-accessible')) {
            return; // Ya está inicializado
        }
        
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $(select).select2({
                theme: 'default',
                width: '100%',
                language: {
                    noResults: function() {
                        return 'No se encontraron resultados';
                    },
                    searching: function() {
                        return 'Buscando...';
                    },
                },
            });
        }
    });
}
