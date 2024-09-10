@extends('admin.common')

@section('body')
    <div class="container">
        <div class="text-center my-5">
            <h2 class="">新增投票活動</h2>
        </div>
        <div class="vote_form_div">
            <form>
                <div class="mb-4">
                    <label for="voteName" class="form-label">投票活動名稱</label>
                    <input type="text" class="form-control" id="voteName" required placeholder="請輸入名稱">
                </div>
                <div class="row mb-4">
                    <div class="col-12 col-md-6 mb-3">
                        <label for="startTime" class="form-label">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar-check" viewBox="0 0 16 16">
                                <path d="M10.854 7.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708 0"/>
                                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
                            </svg>
                            &nbsp;投票時間</label>
                        <input type="text" class="form-control" id="startTime" placeholder="" value="" required="">
                        <div class="invalid-feedback">
                            Valid first name is required.
                        </div>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label for="endTime" class="form-label">結束時間</label>
                        <input type="text" class="form-control" id="endTime" placeholder="" value="" required="">
                        <div class="invalid-feedback">
                            Valid last name is required.
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" name="manual_control" checked id="flexCheckIndeterminate">
                            <label class="form-check-label" for="flexCheckIndeterminate">手動開啟/關閉投票活動</label>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="voteName" class="form-label">設定有哪些候選人</label>
                    <div class="candidate_div">
                        <div class="input-group mb-3">
                            <span class="input-group-text num">1</span>
                            <input type="text" class="form-control candidate_input" placeholder="請輸入候選人名稱" required>
                            <input type="text" class="form-control candidate_school" placeholder="請輸入候選人學校">
                            <span class="input-group-text"></span>
                        </div>
                    </div>
                    <span>
                        <button id="add_candidate" type="button" class="btn btn-outline-success">
                            <svg width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/>
                            </svg>
                            Button
                        </button>
                    </span>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="country" class="form-label">每人最多可以投幾票</label>
                        <select class="form-select" required="" name="max_vote">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3" selected>3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="country" class="form-label">最多選出幾名winner</label>
                        <select class="form-select" required="" name="max_winner">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3" selected>3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a valid country.
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6 col-12">
                        <label for="qrcode" class="form-label">設定發放的Qrcode數</label>
                        <input type="number" class="form-control" id="qrcode" required placeholder="0">
                    </div>
                    {{-- <div class="col-sm-4">
                        <label for="voteName" class="form-label">設定當選名額</label>
                        <input type="number" class="form-control" id="qrcode" required placeholder="0">
                    </div> --}}
                </div>
                <div class="text-center mb-4">
                    <button id="btnSubmit" type="button" class="btn btn-primary">確認送出</button>
                    <button type="cancel" class="btn btn-secondary">取消</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script_js')
<script>
    $(function() {
        $('#add_candidate').on('click', function() {

            let lastText = parseInt($('.num').last().text());

            $('.candidate_div').append(`
                <div class="input-group mb-3">
                    <span class="input-group-text num">${lastText + 1}</span>
                    <input type="text" class="form-control candidate_input" placeholder="請輸入候選人名稱" required>
                    <input type="text" class="form-control candidate_school" placeholder="請輸入候選人學校">
                    <span class="input-group-text btnDeleteRow">
                        <svg width="16" height="16" fill="currentColor" class="bi bi-x-lg delete" viewBox="0 0 16 16">
                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                        </svg>
                    </span>
                </div>
            `);
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

            if(parseInt(qrcodeCount) <= 0) {
                alert('qrcode張數設定錯誤')
                return;
            }

            let candidates = []
            $('.candidate_input').each(function(idx) {
                if($(this).val() !== '') {
                    let tmp = {}
                    tmp.name = $(this).val()
                    tmp.school = $('.candidate_school').eq(idx).val()
                    candidates.push(tmp)
                }
            })

            if(candidates.length < 1) {
                alert('請填寫候選人選項')
                return;
            }

            if (endTime.isBefore(startTime) || endTime.isSame(startTime)) {
                alert('結束時間不可以早於開始時間')
                return;
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
            console.log(JSON.stringify(post_data))

            $.ajax({
                type: 'POST',
                url: "{{ route('create.vote') }}",
                contentType: 'application/json',
                data: JSON.stringify(post_data),
            }).done(function(re) {
                console.log(re)
                alert('新增成功')
                location.href = "{{ url('admin') }}"
            }).fail(function(re) {
                alert('發生錯誤：' + re.responseText);
            });
        })

        $('.candidate_div').on('click', '.btnDeleteRow', function() {
            $(this).closest('.input-group').remove()
        })

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

    })
</script>
@endsection