@extends('admin.common')

@section('body')
<div class="container admin_container">
    <h3 class="fw-bold me-3 text-center">[編輯]</h3>
    <div class="shadow block mb-5">
        <div class="mb-5">
            <div>
                <div class="edit_time fs-5 mb-4">
                    <label for="voteName" class="form-label">
                        <strong>投票活動名稱</strong>
                    </label>
                    <input type="text" class="form-control" id="voteName" required value="{{ $vote_event->event_name }}">
                </div>
                <div class="edit_time row fs-5">
                    <span class="col-6">
                        <label for="startTime" class="form-label">
                            <strong>投票開始</strong> 
                        </label>
                        <input type="text" class="form-control" id="startTime" value="{{ $vote_event->start_time }}" disabled>
                    </span>
                    <span class="col-6">
                        <label for="startTime" class="form-label">
                            <strong>投票結束</strong> 
                        </label>
                        <input type="text" class="form-control" id="endTime" value="{{ $vote_event->end_time }}" disabled>
                    </span>
                </div>
                <div class="form-check">
                    <input class="form-check-input" id="checkOpen" type="checkbox" value="1" name="manual_control" {{ $vote_event->manual_control ? 'checked' : '' }}>
                    <label class="form-check-label" for="checkOpen">手動開啟/關閉投票活動</label>
                </div>
            </div>
        </div> 
        <div class="col-12 mb-5 px-3">
            <table class="table table-bordered detail_table">
                <thead>
                    <tr>
                        <th width="33%" scope="col">每票最多圈選人數</th>
                        <th width="33%" scope="col">獎勵名額</th>
                        <th width="33%" scope="col">產生的選票數量</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td scope="col">
                            <select class="form-select" required="" name="max_vote">
                                <option value="1" {{ $vote_event->max_vote_count == "1" ? 'selected' : '' }}>1</option>
                                <option value="2" {{ $vote_event->max_vote_count == "2" ? 'selected' : '' }}>2</option>
                                <option value="3" {{ $vote_event->max_vote_count == "3" ? 'selected' : '' }}>3</option>
                                <option value="4" {{ $vote_event->max_vote_count == "4" ? 'selected' : '' }}>4</option>
                                <option value="5" {{ $vote_event->max_vote_count == "5" ? 'selected' : '' }}>5</option>
                            </select>
                        </td>
                        <td scope="col">
                            <select class="form-select" required="" name="max_winner">
                                <option value="1" {{ $vote_event->number_of_winners == "1" ? 'selected' : '' }}>1</option>
                                <option value="2" {{ $vote_event->number_of_winners == "2" ? 'selected' : '' }}>2</option>
                                <option value="3" {{ $vote_event->number_of_winners == "3" ? 'selected' : '' }}>3</option>
                                <option value="4" {{ $vote_event->number_of_winners == "4" ? 'selected' : '' }}>4</option>
                                <option value="5" {{ $vote_event->number_of_winners == "5" ? 'selected' : '' }}>5</option>
                            </select>
                        </td>
                        <td scope="col">
                            <input type="number" class="form-control" id="qrcode" min="1" value="{{ $vote_event->number_of_qrcodes }}">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="card-text fs-5 fw-bold">候選人</p>
        <div class="col-12 mb-3 px-3">
            <table class="table table-bordered table-hover detail_table">
                <thead>
                    <tr>
                        <th scope="col" width="15%">#</th>
                        <th scope="col">候選人學校</th>
                        <th scope="col">候選人名稱</th>
                    </tr>
                </thead>
                <tbody class="candidate_tbody">
                    @foreach ($candidates as $cand)
                    <tr>
                        <th scope="row">
                            <input type="number" class="form-control candidate_number" value="{{ $cand['number'] }}">
                        </th>
                        <td>
                            <input type="text" class="form-control candidate_school" value="{{ $cand['school'] }}">
                        </td>
                        <td>
                            <input type="text" class="form-control candidate_name" value="{{ $cand['name'] }}">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <span>
                <button id="add_candidate" type="button" class="btn btn-outline-success">
                    <svg width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/>
                    </svg>
                    新增
                </button>
            </span>
        </div>
        <div class="text-center my-4">
            <button id="btnSubmit" class="btn btn-primary">確認送出</button>
            <button id="btnCancel" class="btn btn-secondary">取消</button>
        </div>
    </div>
@endsection

@section('script_js')

<script>
    $('#add_candidate').on('click', function() {
        $('.candidate_tbody').append(`
            <tr>
                <th scope="row">
                    <input type="number" class="form-control candidate_number" value="">
                </th>
                <td>
                    <input type="text" class="form-control candidate_school" value="">
                </td>
                <td>
                    <input type="text" class="form-control candidate_name" value="">
                </td>
            </tr>
        `);
    })

    $('input[name=manual_control]').on('change', function() {
            if($('input[name=manual_control]').prop('checked') === false) {
                $('#startTime').prop('disabled', false)
                $('#endTime').prop('disabled', false)
            } else {
                $('#startTime').prop('disabled', true)
                $('#endTime').prop('disabled', true)
            }
        })

    $('#btnCancel').on('click', function() {
        const currentUrl = window.location.href;
        const newUrl = currentUrl.replace(/\/edit$/, "");
        window.location.href = newUrl;
    })

    $('#btnSubmit').on('click', function() {
        let voteName = $('#voteName').val()
        let start = $('input[id="startTime"]').val()
        let end = $('input[id="endTime"]').val()
        let startTime = moment(start, "YYYY-MM-DD HH:mm:ss");
        let endTime = moment(end, "YYYY-MM-DD HH:mm:ss");
        let qrcodeCount = $('#qrcode').val()
        let boolManual = $('input[name=manual_control]').prop('checked') ? 1 : 0
        let max_vote = $('select[name=max_vote]').val()
        let max_winner = $('select[name=max_winner]').val()

        let error = [];
        error = validateInput(startTime, endTime, boolManual)

        if(error.length > 0) {
            let error_msg = ''
            error.forEach(error => {
                error_msg += `<p>${error}</p>`
            });
            Swal.fire({
                title: "Notice",
                html: error_msg,
            });

            return;
        }

        let candidates = []
        $('.candidate_number').each(function(idx) {
            if($(this).val() !== '') {
                let tmp = {}
                tmp.number = $(this).val()
                tmp.name = $('.candidate_name').eq(idx).val()
                tmp.school = $('.candidate_school').eq(idx).val()
                candidates.push(tmp)
            }
        })

        if(candidates.length < 1) {
            alert('必須至少要有一位候選人')
        }

        const post_data = {}
        post_data._token = "{{ csrf_token() }}"
        post_data.vote_name = voteName
        post_data.start = start
        post_data.end = end
        post_data.candidates = candidates
        post_data.qrcode_count = qrcodeCount
        post_data.manual_control = boolManual
        post_data.max_vote = max_vote
        post_data.max_winner = max_winner

        console.log(post_data)

        $.ajax({
            type: 'PUT',
            url: "{{ route('vote.edit', ['event_id' => $vote_event->event_id]) }}",
            contentType: 'application/json',
            data: JSON.stringify(post_data),
        }).done(function(re) {
            console.log(re)
            alert('更新成功')
            const currentUrl = window.location.href;
            const newUrl = currentUrl.replace(/\/edit$/, "");
            window.location.href = newUrl;
        }).fail(function(re) {
            console.log(re)
        });
    })

    const validateInput = (startTime, endTime, boolManual) => {
            let error = []

            if($('#voteName').val().trim() == '') {
                error.push('請填寫投票名稱')
            }

            if(boolManual == 0) {
                if(endTime.isBefore(startTime) || endTime.isSame(startTime)) {
                    error.push('結束時間不可以早於開始時間')
                }
            }

            $('.input-group').each(function(index) {
                let num = $(this).find('.candidate_number').val()
                let school = $('.candidate_school').eq(index).val().trim()
                let name = $('.candidate_name').eq(index).val().trim()

                if(num == '' && school == '' && name == '') {
                    return;
                } else if(num == '' || school == '' || name == '') {
                    error.push('請填寫候選人資訊')
                    return false;
                }
            })

            return error
        }

    const set_datepicker = () => {
            let today = new Date();
            let str_today = dateFormat(today)

            const config = {
                singleDatePicker: true,
                timePicker: true,
                timePicker24Hour: true,
                timePickerSeconds: true,
                startDate: moment().startOf('hour'),
                locale: {
                    format: 'YYYY-MM-DD H:mm:ss',
                    applyLabel: "確認",
                    cancelLabel: "取消",
                    daysOfWeek: [
                        "日",
                        "一",
                        "二",
                        "三",
                        "四",
                        "五",
                        "六"
                    ],
                    monthNames: [
                        "1 月",
                        "2 月",
                        "3 月",
                        "4 月",
                        "5 月",
                        "6 月",
                        "7 月",
                        "8 月",
                        "9 月",
                        "10 月",
                        "11 月",
                        "12 月"
                    ],
                }
            }

            $('input[id="startTime"]').daterangepicker(config, function(start, end, label) {
                console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
            });

            $('input[id="endTime"]').daterangepicker(config);
        }

        set_datepicker()

</script>

@endsection
