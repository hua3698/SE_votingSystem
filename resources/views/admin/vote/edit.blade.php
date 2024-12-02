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
                        <input type="text" class="form-control" id="startTime" value="{{ $vote_event->start_time }}" required="">
                        <div class="invalid-feedback">
                            Valid first name is required.
                        </div>
                    </span>
                    <span class="col-6">
                        <label for="startTime" class="form-label">
                            <strong>投票結束</strong> 
                        </label>
                        <input type="text" class="form-control" id="endTime" value="{{ $vote_event->end_time }}" required="">
                        <div class="invalid-feedback">
                            Valid first name is required.
                        </div>
                    </span>
                </div>
            </div>
        </div> 
        <div class="col-12 mb-5 px-3">
            <table class="table table-bordered detail_table">
                <thead>
                    <tr>
                        <th scope="col">每人最多可以投幾票</th>
                        <th scope="col">最多選出幾名winner</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="col">
                            <select class="form-select" required="" name="max_vote">
                                <option value="1" {{ $vote_event->max_vote_count == "1" ? 'selected' : '' }}>1</option>
                                <option value="2" {{ $vote_event->max_vote_count == "2" ? 'selected' : '' }}>2</option>
                                <option value="3" {{ $vote_event->max_vote_count == "3" ? 'selected' : '' }}>3</option>
                                <option value="4" {{ $vote_event->max_vote_count == "4" ? 'selected' : '' }}>4</option>
                                <option value="5" {{ $vote_event->max_vote_count == "5" ? 'selected' : '' }}>5</option>
                            </select>
                        </th>
                        <th scope="col">
                            <select class="form-select" required="" name="max_winner">
                                <option value="1" {{ $vote_event->number_of_winners == "1" ? 'selected' : '' }}>1</option>
                                <option value="2" {{ $vote_event->number_of_winners == "2" ? 'selected' : '' }}>2</option>
                                <option value="3" {{ $vote_event->number_of_winners == "3" ? 'selected' : '' }}>3</option>
                                <option value="4" {{ $vote_event->number_of_winners == "4" ? 'selected' : '' }}>4</option>
                                <option value="5" {{ $vote_event->number_of_winners == "5" ? 'selected' : '' }}>5</option>
                            </select>
                        </th>
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
                        <th scope="col">候選人名稱</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($candidates as $cand)
                    <tr>
                        <th scope="row">
                            <input type="number" class="form-control" value="{{ $cand['number'] }}">
                        </th>
                        <td>
                            <input type="text" class="form-control" value="{{ $cand['name'] }}">
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
    $(function() {
        $('#add_candidate').on('click', function() {
        })

        $('#btnSubmit').on('click', function() {
            alert('還沒好啦')
        })

        $('#btnCancel').on('click', function() {
            history.back()
        })

        const set_datepicker = {
            config : function(time) {
                return {
                    singleDatePicker: true,
                    timePicker: true,
                    timePicker24Hour: true,
                    timePickerSeconds: true,
                    startDate: time,
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
            }
        }

        $('input[id="startTime"]').daterangepicker(set_datepicker.config("{{ $vote_event->start_time }}"));
        $('input[id="endTime"]').daterangepicker(set_datepicker.config("{{ $vote_event->end_time }}"));
    })
</script>

@endsection
