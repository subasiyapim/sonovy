<template>
  <div
    class="switch-container"
    :class="{ 'switch-on': modelValue, 'switch-off': !modelValue }"
    @click="toggle"
  >
    <div class="switch-knob">
        <div class="switch-knob-inner"></div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Switch",
  props: {
    modelValue: {
      type: Boolean,
      default: false,
    },
    onLabel: {
      type: String,
      default: "On",
    },
    offLabel: {
      type: String,
      default: "Off",
    },
  },
  emits: ["update:modelValue"],
  setup(props, { emit }) {
    const toggle = () => {
      emit("update:modelValue", !props.modelValue);
      emit("change", !props.modelValue);
    };
    return { toggle };
  },
};
</script>

<style scoped>
.switch-container {
  display: inline-flex;
  align-items: center;
  cursor: pointer;
  user-select: none;
  padding:0px 2px;
  border-radius: 16px;
  background-color: #ccc;
  transition: background-color 0.3s ease;
  width: 28px;
  height:16px;
  justify-content: space-between;
}

.switch-container.switch-on {
  background-color: var(--dark-green-700);
}

.switch-container.switch-off {
  background-color: #ccc;
}

.switch-knob {
  width: 12px;
  height: 12px;
  background-color: #fff;
  border-radius: 50%;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  transition: transform 0.3s ease;
  display:flex;
  align-items:center;
  justify-content:center;
}
.switch-container.switch-on .switch-knob-inner{
    background: var(--dark-green-700);
    width:4px;
    height:4px;
    border-radius:50%;
}

.switch-container.switch-on .switch-knob  {
  transform: translateX(12px);
}

.switch-container.switch-off .switch-knob {
  transform: translateX(0);
}

.switch-label {
  font-size: 12px;
  color: #fff;
  margin-left: 8px;
}
</style>
