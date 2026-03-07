<div class="modal fade" id="addStoreSongModal" tabindex="-1" role="dialog" aria-labelledby="addStoreSongModal"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Song</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                    <create-store-song save-url="{{ route('admin.store.songs.create', [$store]) }}"
                        success-url="{{ route('admin.store.songs', [$store]) }}">
                        <template v-slot:cancel-link>
                            <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-secondary mr-4">
                                Cancel
                            </button>
                        </template>
                    </create-store-song>
                </div>
            </div>
        </div>
    </div>
</div>
                