<div class="page-header">
    <h1>Add New Sale</h1>
</div>

<div class="form-card">
    <form id="add-auction-form" novalidate>
        <div class="form-row">
            <div class="form-group flex-2">
                <label for="link">Link to Sale</label>
                <div class="input-with-btn">
                    <input type="url" id="link" name="link" placeholder="https://www.auksjonen.no/...">
                    <button type="button" id="scrape-btn" class="btn btn-secondary">
                        <i class="fas fa-magic"></i> Scrape Info
                    </button>
                </div>
            </div>
        </div>

        <div id="scrape-loading" class="scrape-loading hidden">
            <i class="fas fa-spinner fa-spin"></i> Fetching auction info...
        </div>


        <div class="form-row">
            <div class="form-group">
                <label for="production_year">Production Year</label>
                <input type="number" id="production_year" name="production_year" placeholder="e.g. 2021" min="1900" max="<?= date('Y') + 1 ?>">
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <input type="text" id="category" name="category" placeholder="e.g. Electronics, Vehicles">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group flex-2">
                <label for="title">Title <span class="required">*</span></label>
                <input type="text" id="title" name="title" placeholder="Auction title" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="date_added">Date Added <span class="required">*</span></label>
                <input type="date" id="date_added" name="date_added" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group">
                <label for="finish_date">Finish Date</label>
                <input type="date" id="finish_date" name="finish_date">
                <label class="checkbox-label">
                    <input type="checkbox" id="sold" name="sold"> Mark as sold — move to Finished Sales
                </label>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="investment_cost">Investment Cost (kr) <span class="required">*</span></label>
                <input type="number" id="investment_cost" name="investment_cost" placeholder="0" min="0" step="1" required>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-large">
                <i class="fas fa-plus-circle"></i> Add new sale
            </button>
        </div>
    </form>
    <div id="form-message" class="form-message hidden"></div>
</div>
