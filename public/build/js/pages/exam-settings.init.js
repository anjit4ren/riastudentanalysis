
$(document).ready(function () {

    let table = $('#examSettingsTable').DataTable({
        ajax: {
            url: '/exam-settings/list',
            data: d => {
                d.academic_year_id = $('#academicFilter').val();
                d.is_active = $('#statusFilter').val();
            },
            dataSrc: function (json) {
                // Return the array of exam_settings
                return json.data.exam_settings;
            }
        },
        columns: [{
            data: null,
            render: (d, t, r, m) => m.row + 1
        },
        {
            data: 'title'
        },
        {
            data: 'academic_year.name'
        },
        {
            data: 'description'
        },
        {
            data: 'is_active',
            render: d => d ? '<span class="badge bg-success">Active</span>' :
                '<span class="badge bg-danger">Inactive</span>'
        },
        {
            data: null,
            render: row => `
                <button class="btn btn-sm btn-primary edit-exam" data-id="${row.id}">Edit</button>
                <button class="btn btn-sm btn-warning toggle-exam" data-id="${row.id}">${row.is_active ? 'Deactivate' : 'Activate'}</button>
                <button class="btn btn-sm btn-danger delete-exam" data-id="${row.id}">Delete</button>
            `
        }
        ]
    });


    $('#searchTableList').keyup(function () {
        table.search(this.value).draw();
    });
    $('#academicFilter, #statusFilter').change(() => table.ajax.reload());

    // Save exam
    $('#examForm').submit(function (e) {
        e.preventDefault();
        let id = $('#examId').val();
        let url = id ? '/exam-settings/update/' + id : '/exam-settings/store';
        $.post(url, {
            id,
            title: $('#title-input').val(),
            academic_year_id: $('#academic-input').val(),
            description: $('#description-input').val(),
            is_active: $('#status-input').is(':checked') ? 1 : 0,
            _token: $('meta[name="csrf-token"]').attr('content')
        }).done(() => {
            $('#examModal').modal('hide');
            table.ajax.reload();
            toastr.success('Exam saved successfully');
        }).fail(() => toastr.error('Error saving exam'));
    });

    // Edit
    $(document).on('click', '.edit-exam', function () {
        $.get('/exam-settings/details/' + $(this).data('id'), res => {
            $('#examId').val(res.data.id);
            $('#title-input').val(res.data.title);
            $('#academic-input').val(res.data.academic_year_id).trigger('change');
            $('#description-input').val(res.data.description);
            $('#status-input').prop('checked', res.data.is_active);
            $('#examModal').modal('show');
        });
    });

    // Toggle
    $(document).on('click', '.toggle-exam', function () {
        $.post('/exam-settings/toggle-status/' + $(this).data('id'), {
            _token: $('meta[name="csrf-token"]').attr('content')
        }, () => {
            table.ajax.reload();
            toastr.success('Status updated');
        });
    });

    // Delete
    $(document).on('click', '.delete-exam', function () {
        if (confirm('Are you sure?')) {
            $.ajax({
                url: '/exam-settings/delete/' + $(this).data('id'),
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                }
            })
                .done(() => {
                    table.ajax.reload();
                    toastr.success('Exam deleted');
                })
                .fail(() => toastr.error('Error deleting exam'));
        }
    });
});
