<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ChatGPT Integration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header text-center">
                <h5>Chat with Chatgpt</h5>
            </div>
            <div class="card-body" style="height: 400px; overflow-y: auto;" id="chatWindow">
                
            </div>
            <div class="card-footer">
                <form id="chatForm">
                    @csrf
                    <div class="input-group">
                        <input type="text" id="messageInput" class="form-control" placeholder="Type your message..." required>
                        <button class="btn btn-primary" type="submit" id="sendButton">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#chatForm').submit(function (e) {
                e.preventDefault();
                const message = $('#messageInput').val();
                if (!message) return;

                $('#chatWindow').append(
                    `<div class="text-end mb-2"><span class="badge bg-primary">You:</span> ${message}</div>`
                );

                $('#messageInput').val('');

                $.ajax({
                    url: "{{ url('/chatmsg') }}",
                    method: 'POST',
                    
                    data: { message: message },
                    success: function(response) {
                        $('#chatWindow').append(`<div class="text-start mb-2"><span class="badge bg-success">Chatgpt:</span> ${response.message}</div>`);
                        $('#chatWindow').scrollTop($('#chatWindow')[0].scrollHeight);
                    },
                    error: function(xhr) {

                        console.error('Error:', xhr.responseJSON.error || 'An unexpected error occurred.');


                        $('#chatWindow').append(`<div class="text-start mb-2 text-danger"><strong>Error:</strong> ${xhr.responseJSON.error || 'An unexpected error occurred.'}</div>`);
                    }
                });

            });
        });
    </script>
</body>
</html>
