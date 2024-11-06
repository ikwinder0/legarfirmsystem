<!-- Modal -->
<div class="modal fade" id="caseDetailStatus" tabindex="-1" aria-labelledby="caseDetailStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="caseDetailStatusModalLabel">Change Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-group" action="{{route('case_detail.change_status')}}" method="post">
                @csrf
                <div class="modal-body">
                    <input type="hidden" value="" id="modalId" name="id">
                    <div class="row">
                        <div class="col">
                            <select class="form-control" id="caseStatus" name="status">
                                <option>select Status</option>
                                @foreach(\App\Models\CaseDetail::_STATUS_OPTIONS as $status)
                                    <option value="{{ $status }}">{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="runnerTaskStatus" tabindex="-1" aria-labelledby="runnerTaskStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="caseDetailStatusModalLabel">Change Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-group" action="{{route('runner_task.change_status')}}" method="post">
                @csrf
                <div class="modal-body">
                    <input type="hidden" value="" id="runnerId" name="id">
                    <div class="row">
                        <div class="col">
                            <select class="form-control" id="runnerStatus" name="status">
                                <option>select Status</option>
                                @foreach(\App\Models\RunnerTask::_STATUS_OPTIONS as $status)
                                    <option value="{{ $status }}">{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>