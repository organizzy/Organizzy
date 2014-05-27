<?php
/**
 *
 * @var EventController $this
 * @var EventSetupVoteForm $model
 */

?>
<script>

</script>
<form class="content-padded">
    <div class="row">
        <label>Duration</label>
        <input type="number" name="" value="60">
    </div>
    <div class="row">
        <label>Begin Date</label>
        <input type="date" name="" value="<?php echo date('Y-m-d') ?>">
    </div>

    <div class="row">
        <label>End Date</label>
        <input type="date" name="" value="<?php echo date('Y-m-d') ?>">
    </div>

    <div class="row">
        <label>Begin Time</label>
        <input type="time" name="" value="08:00:00">
    </div>

    <div class="row">
        <label>End Time</label>
        <input type="time" name="" value="18:00:00">
    </div>

    <div class="row row-button">
        <button type="submit" class="btn btn-block">Next &gt;</button>
    </div>
</form>

<div>

</div>