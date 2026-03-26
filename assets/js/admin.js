jQuery(document).ready(function($) {
    console.log(bs_breakpoints_vars);
    var type = bs_breakpoints_vars.type_selector;
    var choices = bs_breakpoints_vars.choices_wrapper;
    $(type).each(function(){

        // Initially hide choices textarea if the type is not 'select'
        if ($(this).val() !== 'select') {
            $(choices).hide();
        }else{
            $(choices).show();
        }

        if (['true_false', 'image', 'color_picker', 'select'].includes($(this).val())) {
            $(".bs-breakpoints-choices-defaults").hide();
        }else{
            $(".bs-breakpoints-choices-defaults").show();
        }

        // Show/hide choices textarea based on selected type
        $(document).on('change', type, function() {
            var selectedType = $(this).val();
            if (selectedType === 'select') {
                $(choices).show();
            } else {
                $(choices).hide();
            }
            if (['true_false', 'image', 'color_picker', 'select'].includes(selectedType)) {
                $(".bs-breakpoints-choices-defaults").hide();
            }else{
                $(".bs-breakpoints-choices-defaults").show();
            }
        }).trigger("change");

    });
});