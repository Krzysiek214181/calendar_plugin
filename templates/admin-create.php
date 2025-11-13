<div class="wrap">
  <?php if (isset($_GET['submition_status'])): ?>
    <?php if ($_GET['submition_status'] === 'success'): ?>
      <div class="notice notice-success is-dismissible">
        <p><strong>Event created successfully!</strong></p>
      </div>
    <?php elseif ($_GET['submition_status'] === 'error'): ?>
      <div class="notice notice-error is-dismissible">
        <p><strong>There was an error saving the event.</strong></p>
      </div>
    <?php endif; ?>
  <?php endif; ?>
  <h2>Utwórz Wydarzenie</h2>
  <form id="calendar_event_creation_form" method="post" action="<?php echo esc_url(admin_url('admin-post.php'))?>" class="calendar-event-creation-form">
    <?php wp_nonce_field('calendar-event-creation-form', 'calendar-event-creation-nonce')?>
    <input type="hidden" name="action" value="calendar_event_creation_submit">
    <table class="form-table">
      <tr>
        <th><label for="type">Typ *</label></th>
        <td>
          <select id="type" name="type" required>
            <option value="lesson">Lekcja</option>
            <option value="blocking">Blokujący</option>
          </select>
        </td>
      </tr> 

      <tr>
        <th><label for="event_name">Nazwa Wydarzenia *</label></th>
        <td><input type="text" id="event_name" name="event_name" required placeholder="Język Polski"></td>
      </tr>

      <tr>
        <th><label for="teacher">Nauczyciel</label></th>
        <td><input type="text" id="teacher" name="teacher" placeholder="Prof. Kowalski"></td>
      </tr>

      <tr>
        <th><label for="room">Sala</label></th>
        <td><input type="number" id="room" name="room" placeholder="124"></td>
      </tr>
      <tr>
        <th><label for="class">Klasa</label></th>
        <td><input type="text" id="class" name="class" maxlength="4" placeholder="4a_2"></td>
      </tr>

      <tr>
        <th><label for="start_time">Data Rozpoczęcia *</label></th>
        <td><input type="date" id="start_date" name="start_date" required><input type="time" name="start_time" id="start_time" required></td>
      </tr>

      <tr>
        <th><label for="whole_day">Cały Dzień? *</label></th>
        <td>
          <select id="whole_day" name="whole_day" required onchange="toggleEndTime()">
              <option value="1">Tak</option>
              <option value="0" selected>Nie</option>
          </select>
        </td>
      </tr>

      <tr id="end_time_row">
        <th><label for="end_time">Data Zakończenia *</label></th>
        <td><input type="date" id="end_date" name="end_date"><input id="end_time" name="end_time" type="time"></td>
      </tr>

      <tr>
        <th><label for="recurrence_type">Powtarzalność *</label></th>
        <td>
          <select id="recurrence_type" name="recurrence_type" required>
            <option value="none">Brak</option>
            <option value="daily">Codziennie</option>
            <option value="weekly">Tygodniowo</option>
            <option value="biweekly">Co 2 Tydzień</option>
            <option value="monthly">Miesięcznie</option>
            <option value="yearly">Rocznie</option>
          </select>
        </td>
      </tr>

      <tr>
        <th><label for="recurrence_end">Koniec Powtarzalności</label></th>
        <td><input type="date" id="recurrence_end" name="recurrence_end"></td>
      </tr>
    </table>

    <p><input type="submit" class="button-primary" value="Stwórz Wydarzenie"></p>
  </form>
</div>
<script>
const startTime = document.getElementById("start_time");
const endTimeRow = document.getElementById("end_time_row");
const endDate = document.getElementById("end_date");
const endTime = document.getElementById("end_time");

function toggleEndTime() {
    const wholeDay = document.getElementById("whole_day").value;

  if (wholeDay === "1") {
    endTimeRow.style.display = "none";
    startTime.style.display = "none";
    endTime.required = false;
    endDate.required = false;
} else {
    endTimeRow.style.display = "table-row";
    startTime.style.display = "inline";
    endTime.required = true;
    endDate.required = true;
  }
}
toggleEndTime();
</script>

