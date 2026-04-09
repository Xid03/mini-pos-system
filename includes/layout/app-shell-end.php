        </main>
    </div>
</div>
<div class="sidebar-backdrop" data-sidebar-dismiss></div>
<div class="modal fade" id="confirmActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content confirm-modal">
            <div class="confirm-modal__header">
                <div class="confirm-modal__icon">
                    <i class="bi bi-exclamation-diamond-fill"></i>
                </div>
                <div>
                    <h3 id="confirmActionTitle">Confirm action</h3>
                    <p id="confirmActionMessage">Please confirm that you want to continue.</p>
                </div>
            </div>
            <div class="confirm-modal__actions">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmActionButton">Delete</button>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="<?= htmlspecialchars(url('assets/js/app.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
