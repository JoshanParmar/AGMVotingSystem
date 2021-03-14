$('#manifestoModal').on('show.bs.modal', function (event) {
    let button = $(event.relatedTarget)
    let manifesto_url = button.data('url')
    let candidate_name = button.data('candidate_name')
    let modal = $(this)
    modal.find('.modal-title').text('Manifesto ' + candidate_name)
    console.log(manifesto_url)
    document.getElementById('manifesto-image').src = manifesto_url
    })