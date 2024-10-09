export default {
  mounted(el, binding) {
    el.clickOutsideEvent = function(event) {
      // Check if the click was outside the element and its children
      if (!(el === event.target || el.contains(event.target))) {
        // Call the provided method if a click outside is detected
        binding.value(event);
      }
    };
    // Attach the event listener to the document with 'capture' option set to true
    document.addEventListener("click", el.clickOutsideEvent, true);
  },
  unmounted(el) {
    // Remove the event listener when the element is unmounted
    document.removeEventListener("click", el.clickOutsideEvent, true);
  },
};
