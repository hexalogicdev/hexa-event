/* Script for cloned row in activity */
// document.addEventListener('DOMContentLoaded', function () {
//   var rowCounter = 0;

//   document.getElementById('add_more').addEventListener('click', function () {
//     rowCounter++;

//     var originalRow = document.getElementById('lane_info');
//     var newRow = originalRow.cloneNode(true);
//     newRow.removeAttribute('id');

//     var removeDiv = document.createElement('div');
//     removeDiv.classList.add('col-12');
//     removeDiv.innerHTML = '<a class="delete btn btn-danger btn-sm float-end mt-2" href="#">Remove</a>';
//     newRow.appendChild(removeDiv);

//     // Clear input values and reset selects
//     var inputs = newRow.querySelectorAll('input');
//     inputs.forEach(function (input) {
//       input.value = '';
//     });

//     var selects = newRow.querySelectorAll('select');
//     selects.forEach(function (select) {
//       select.selectedIndex = 0;
//     });

//     newRow.id = 'lane_info_' + rowCounter;

//     // Update name attributes
//     var formElements = newRow.querySelectorAll('input, select');
//     formElements.forEach(function (element) {
//       var name = element.getAttribute('name');
//       if (name) {
//         var newName = name + '_' + rowCounter;
//         element.setAttribute('name', newName);
//       }
//     });

//     document.getElementById('cloned_items').appendChild(newRow);
//   });

//   document.body.addEventListener('click', function (e) {
//     if (e.target && e.target.classList.contains('delete')) {
//       e.preventDefault();
//       e.target.closest('.row').remove();
//     }
//   });
// });

// $(function () {
//   $('input[name="activityType"]').on('change', function () {
//     if ($('#dayspecific').prop('checked')) {
//       $('#days_name').addClass('d-block').removeClass('d-none');
//       $('.days_count').attr('required', true);
//     } else {
//       $('#days_name').addClass('d-none').removeClass('d-block');
//       $('.days_count').removeAttr('required');
//     }

//     if ($('#date_specific').prop('checked')) {
//       $('#date_range').addClass('d-block').removeClass('d-none');
//     } else {
//       $('#date_range').addClass('d-none').removeClass('d-block');
//     }
//   });
// });

$(document).ready(function () {
  var checkbtn = $('.addon-checkbox');

  checkbtn.on('click', function () {
    if ($(this).is(':checked')) {
      alert('Checkbox checked!');
    } else {
      alert('Checkbox unchecked!');
    }
  });
});
$(document).ready(function () {
  const parentItem = $(".menu-item.active").closest(".menu-sub");
  parentItem.closest(".menu-item").addClass('open');

});
document.addEventListener("DOMContentLoaded", function () {
  let form = document.querySelector(".needs-validation");

  if (form) {
    form.addEventListener("submit", function (event) {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
        form.classList.add("was-validated");

        $(".is-invalid").tooltip("dispose");

        let firstInvalidField = form.querySelector(":invalid");
        if (firstInvalidField) {
          firstInvalidField.classList.add("is-invalid");

          $(firstInvalidField).tooltip({
            title: firstInvalidField.validationMessage,
            placement: "bottom",
            trigger: "manual"
          }).tooltip("show");

          firstInvalidField.scrollIntoView({
            behavior: "smooth",
            block: "center"
          });
        }
      } else {
        form.classList.remove("was-validated");
      }
    });

    $(".select2").on("change", function () {
      let select = $(this);
      let selectContainer = select.next(".select2-container");

      if (select.val() && select.val().length > 0) {
        select.removeClass("is-invalid").addClass("is-valid");
        selectContainer.removeClass("is-invalid").addClass("is-valid");

        select.tooltip("dispose");
      } else {
        select.addClass("is-invalid");
        selectContainer.addClass("is-invalid");

        select.tooltip({
          title: "Please select an item from the list",
          placement: "bottom",
          trigger: "manual"
        }).tooltip("show");
      }
    });

    $(".select2").on("select2:select select2:unselect", function () {
      $(this).trigger("change");
    });

    form.querySelectorAll("input, select, textarea").forEach(field => {
      field.addEventListener("change", function () {
        if (field.checkValidity()) {
          field.classList.remove("is-invalid");
          field.classList.add("is-valid");
          $(field).tooltip("dispose");
        }
      });
    });

    const submitButton = form.querySelector(".submit-button");

    form.addEventListener("submit", function (event) {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      } else {
        if (form.dataset.submitted === "true") {
          event.preventDefault();
          return;
        }

        form.dataset.submitted = "true";
        submitButton.disabled = true;
        submitButton.textContent = "Submitting...";
      }
    });

  }
});

$(document).ready(function () {
  function validateSelect(select) {
    let selectContainer = select.next(".select2-container");
    if (select.val() && select.val().length > 0) {
      select.removeClass("is-invalid").addClass("is-valid");
      selectContainer.removeClass("is-invalid").addClass("is-valid");
      select.tooltip("dispose");
    } else {
      select.removeClass("is-valid").addClass("is-invalid");
      selectContainer.addClass("is-invalid");

      select.tooltip({
        title: "Please select an item from the list",
        placement: "top",
        trigger: "manual"
      }).tooltip("show");
    }
  }

  // Validate when changing selection
  $(document).on("change", ".select2", function () {
    validateSelect($(this));
  });

  // Ensure validation works when selecting or unselecting
  $(document).on("select2:select select2:unselect", ".select2", function () {
    $(this).trigger("change");
  });

  // Re-initialize Select2 when dynamically adding new rows
  $(document).on("DOMNodeInserted", function (e) {
    if ($(e.target).hasClass("select2")) {
      $(e.target).select2();
    }
  });
});