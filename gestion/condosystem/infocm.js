

jQuery(document).ready(function($) {
    $('#owner-form').submit(function(e) {
        e.preventDefault();
		
        var formData = $(this).serialize();
        formData += '&action=my_action';
		
		
		var data = {
			'action': 'my_action', 
			'whatever': 12347 
		};
		
		$.post(cm_ajax_object.ajax_url, formData, function(response) {
			
			$('#owner-form')[0].reset();
			if(response!=0)
			{
				alert('Agregado Correctamente');
			}
			else{
				alert('Error');
			}
			
			
		});

      
    });


//////////////////////////////////////////////////////////
    var currentPage = 1;

    function loadOwners(page) {
        $.ajax({
            type: 'POST',
            url: cm_ajax_object.ajax_url,
            data: {
                action: 'load_owners',
                pagina: page,
            },
            success: function(response) {
				console.log(response);
                $('#owners-table').html(response);
                currentPage = page;
            }
        });
    }

    
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        var nextPage = $(this).data('pagina');
        if (nextPage !== currentPage) {
            loadOwners(nextPage);
        }
    });
});


/////////////////////////////////////////////////////////////

jQuery(document).ready(function($) {
    $('#buscar-form').submit(function(e) {
        e.preventDefault();

        var rut = $('#rut').val();

        $.ajax({
            type: 'POST',
            url: cm_ajax_object.ajax_url, 
            data: {
                action: 'buscar_rut',
                rut: rut
            },
            success: function(response) {
                $('#search-result').html(response);
            }
        });
    });
});


//////////////////////////////////////////////////////////
		const rutInput = document.getElementById('rut');
		if (rutInput!=null)
		{
			const rutMessage = document.getElementById('rut-message');
			const rutError = document.getElementById('rut-error');

			rutInput.addEventListener('input', () => {
				const rutValue = rutInput.value.trim();
				const rutPattern = /^(\d{1,9})-(\d|k|K)$/i;

				if (rutPattern.test(rutValue)) {
					rutMessage.textContent = 'Rut válido.';
					rutMessage.style.display = 'block';
					rutError.style.display = 'none';
				} else {
					rutMessage.style.display = 'none';
					rutError.textContent = 'Rut inválido.';
					rutError.style.display = 'block';
				}
			});
		}
		
		const rut_rent = document.getElementById('rut_rent');
		if (rut_rent!=null)
		{
			const rutMessage = document.getElementById('rut-message');
			const rutError = document.getElementById('rut-error');

			rut_rent.addEventListener('input', () => {
				const rutValue = rut_rent.value.trim();
				const rutPattern = /^(\d{1,9})-(\d|k|K)$/i;

				if (rutPattern.test(rutValue)) {
					rutMessage.textContent = 'Rut válido.';
					rutMessage.style.display = 'block';
					rutError.style.display = 'none';
				} else {
					rutMessage.style.display = 'none';
					rutError.textContent = 'Rut inválido.';
					rutError.style.display = 'block';
				}
			});
		}

//////////////////////////////////////////////



jQuery(document).ready(function($) {
    $('#rentday').submit(function(e) {
        e.preventDefault();
		
        var formData = $(this).serialize();
        formData += '&action=insert_rent_data';
	
		
		$.post(cm_ajax_object.ajax_url, formData, function(response) {
			
            $('#rentday')[0].reset();
			
            
            alert('Formulario enviado con éxito');
		});

       
    });
});

/////////////////////////////////////////////////////////////

jQuery(document).ready(function($) {
    $('#buscar-form-rent').submit(function(e) {
        e.preventDefault();

        var rut = $('#rut').val();

        $.ajax({
            type: 'POST',
            url: cm_ajax_object.ajax_url, 
            data: {
                action: 'buscar_rent',
                rut: rut
            },
            success: function(response) {
                $('#search-result').html(response);
            }
        });
    });
});






//////////////////////////////////////////////////////////
jQuery(document).ready(function($) {
    var currentPage = 1;

    function loadRent4day(page) {
        $.ajax({
            type: 'POST',
            url: cm_ajax_object.ajax_url,
            data: {
                action: 'load_rent4day',
                pagina: page,
            },
            success: function(response) {
				console.log(response);
                $('#rent4day-table').html(response);
                currentPage = page;
            }
        });
    }

    
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        var nextPage = $(this).data('pagina');
        if (nextPage !== currentPage) {
            loadRent4day(nextPage);
        }
    });
});

//////////////////////////////////////////////////////////
jQuery(document).ready(function($) {
    var currentPage = 1;

    function loadVisit(page) {
        $.ajax({
            type: 'POST',
            url: cm_ajax_object.ajax_url,
            data: {
                action: 'load_visit',
                pagina: page,
            },
            success: function(response) {
				console.log(response);
                $('#visit-table').html(response);
                currentPage = page;
            }
        });
    }

   
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        var nextPage = $(this).data('pagina');
        if (nextPage !== currentPage) {
            loadVisit(nextPage);
        }
    });
});

//////////////////////////////////////////////////

jQuery(document).ready(function($) {
    $('#visit-form').submit(function(e) {
        e.preventDefault();
		
		
        var formData = $(this).serialize();
        formData += '&action=visit_action';
		
		
		var data = {
			'action': 'visit_action' 
		};
		
		$.post(cm_ajax_object.ajax_url, formData, function(response) {
			
			$('#visit-form')[0].reset();
			if(response!=0)
			{
				alert('Agregado Correctamente');
			}
			else{
				alert('Error');
			}
			
			
		});

      
    });
});

jQuery(document).ready(function($) {
    $('#problem-form').submit(function(e) {
        e.preventDefault();
		
		
        var formData = $(this).serialize();
        formData += '&action=problem_action';
		
		
		var data = {
			'action': 'problem_action' 
		};
		
		$.post(cm_ajax_object.ajax_url, formData, function(response) {
			
			$('#problem-form')[0].reset();
			if(response!=0)
			{
				alert(response);
			}
			else{
				alert(response);
			}
			
			
		});

      
    });
});