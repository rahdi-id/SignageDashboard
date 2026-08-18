@extends('layouts.main')
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Promotion Media ({{ $promotion->name }})</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            @if (session()->has('success'))
                                <div class="alert alert-success alert-dismissible show fade">
                                    <div class="alert-body">
                                        <button class="close" data-dismiss="alert">
                                            <span>&times;</span>
                                        </button>
                                        {{ session('success') }}
                                    </div>
                                </div>
                            @endif
                            <div class="col-sm-12 col-md-6 col-lg-2 px-0 py-3">
                                <a href="{{ route('promotion-media.create', $promotion->id) }}" id="btn-modal"
                                    class="btn btn-block btn-icon icon-left btn-primary"><i class="fas fa-plus"></i>
                                    Add Promotion Media</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped" id="table-1">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Title</th>
                                            <th>Media Preview</th>
                                            <th>Type</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('js')
    <script>
        var promotion = {!! json_encode($promotion) !!};

        var t = $('#table-1').DataTable({
            "ajax": {
                url: "{{ url('promotion-medias/data') }}" + "/" + promotion['id'],
            },
            "responsive": true,
            "processing": true,
            "columnDefs": [{
                "targets": -1,
                "data": null,
                "defaultContent": "<button id='delete' class='btn btn-icon icon-left btn-danger' data-toggle='modal' data-target='#deleteModal'><i class='fas fa-trash'></i> Delete</button>"
            }, {
                "targets": 0,
                "defaultContent": ""
            }],
            "columns": [{
                    data: ""
                },
                {
                    data: "title"
                },
                {
                    data: null,
                    render: function(promotionMedia) {
                        if (promotionMedia.type == 'Image') {
                            var url = "{{ asset('images/:image') }}";
                            url = url.replace(':image', promotionMedia.name);
                            return "<img width='150px' alt='image' src='" + url + "'>";
                        } else if (promotionMedia.url_youtube) {
                            // Extract YouTube video ID from any YouTube URL format
                            var videoId = extractYoutubeId(promotionMedia.url_youtube);
                            if (videoId) {
                                var thumb = "https://img.youtube.com/vi/" + videoId + "/mqdefault.jpg";
                                var embedUrl = "https://www.youtube.com/embed/" + videoId;
                                return "<a href='" + embedUrl + "' target='_blank'>" +
                                       "<img width='150px' alt='YouTube' src='" + thumb + "' " +
                                       "style='border-radius:4px;cursor:pointer;'>" +
                                       "<br><small class='text-danger'><i class='fab fa-youtube'></i> YouTube</small>" +
                                       "</a>";
                            }
                            return "<span class='text-muted'>Invalid URL</span>";
                        } else if (promotionMedia.name) {
                            var videoUrl = "{{ asset('videos/:video') }}";
                            videoUrl = videoUrl.replace(':video', promotionMedia.name);
                            return "<video width='150px' controls>" +
                                   "<source src='" + videoUrl + "'>" +
                                   "</video>";
                        }
                        return '';
                    }
                },
                {
                    data: "type"
                },
                {
                    data: null
                },
            ]
        })

        t.on('order.dt search.dt', function() {
            let i = 1;

            t.cells(null, 0, {
                search: 'applied',
                order: 'applied'
            }).every(function(cell) {
                this.data("<p class ='text-primary'>" + i++ + "</p>");
            });
        }).draw();

        $('#table-1 tbody').on('click', '#delete', function() {
            var data = t.row($(this).parents('tr')).data();
            var url = '{{ route("promotion-media.destroy",[":id",":mediaId"]) }}';
            url = url.replace(':id', promotion['id'])
            url = url.replace(':mediaId', data['id'])
            $("#deleteForm").attr("action", url)
        });

        /**
         * Extract YouTube video ID from various URL formats:
         * - https://youtu.be/VIDEO_ID
         * - https://youtu.be/VIDEO_ID?si=xxx
         * - https://www.youtube.com/watch?v=VIDEO_ID
         * - https://www.youtube.com/watch?v=VIDEO_ID&feature=xxx
         */
        function extractYoutubeId(url) {
            if (!url) return null;
            var patterns = [
                /youtu\.be\/([a-zA-Z0-9_-]{11})/,
                /youtube\.com\/watch\?.*v=([a-zA-Z0-9_-]{11})/,
                /youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/,
                /youtube\.com\/v\/([a-zA-Z0-9_-]{11})/
            ];
            for (var i = 0; i < patterns.length; i++) {
                var match = url.match(patterns[i]);
                if (match) return match[1];
            }
            return null;
        }
    </script>
@endsection
