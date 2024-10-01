@extends('admin.common')

@section('body')
<div class="container admin_container">
    <h2 class="text-center mb-5"><span class="fw-bold">[編輯]</span> {{ $vote_event->event_name }} </h2>
    <div class="shadow block mb-5">
        <div class="mb-5">
            <div>
                <div class="edit_time row fs-5">
                    <span class="col-6">
                        <label for="startTime" class="form-label">
                            <strong>投票開始：</strong> 
                        </label>
                        <input type="text" class="form-control" id="startTime" placeholder="" value="{{ $vote_event->start_time }}" required="">
                        <div class="invalid-feedback">
                            Valid first name is required.
                        </div>
                        
                    </span>
                    <span class="col-6">
                        <label for="startTime" class="form-label">
                            <strong>投票結束：</strong> 
                        </label>
                        <input type="text" class="form-control" id="startTime" placeholder="" value="{{ $vote_event->end_time }}" required="">
                        <div class="invalid-feedback">
                            Valid first name is required.
                        </div>
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
                        <th scope="col">每人最多可以投幾票</th>
                        <th scope="col">最多選出幾名winner</th>
                        <th scope="col">設定發放的Qrcode數</th>
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
                        <th scope="col">
                            <input type="number" class="form-control" id="qrcode" required value="{{ $vote_event->number_of_qrcodes }}">
                        </th>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="card-text fs-5">
            <span class="bar">
                <strong>候選人</strong>
            </span>
        </p>
        <div class="col-12 mb-3 px-3">
            <table class="table table-bordered table-hover detail_table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">候選人名稱</th>
                        <th scope="col">候選人學校</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($candidates as $cand)
                    <tr>
                        <th scope="row">{{ $cand['number'] }}</th>
                        <td>{{ $cand['name'] }}</td>
                        <td>{{ $cand['school'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script_js')

<script>
    
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
