const settingsPanel = document.getElementById("element-settings");
const btn = document.getElementById("select-btn");
let selectedElement = null;

function onHover(e) {
  if (e.target === btn || e.target === settingsPanel) {
    return;
  }

  e.target.classList.add("hovered");
}

function onOut(e) {
  if (e.target === btn || e.target === settingsPanel) {
    return;
  }

  e.target.classList.remove("hovered");
}

function onSelect(e) {
  if (e.target === btn || e.target === settingsPanel) {
    return;
  }

  btn.textContent = "Selecionar";

  e.preventDefault();

  selectedElement = e.target;

  mountPanel();

  onOut(e);
  document.removeEventListener("mouseover", onHover);
  document.removeEventListener("mouseout", onOut);
  document.removeEventListener("click", onSelect);
}

function selectElement() {
  btn.textContent = "Selecionando";

  document.addEventListener("click", onSelect);

  document.addEventListener("mouseover", onHover);

  document.addEventListener("mouseout", onOut);
}

function mountPanel() {
  settingsPanel.innerHTML = "";
  if (!settingsPanel.classList.contains("visible")) {
    settingsPanel.classList.add("visible");
  }
  const { backgroundColor, color, borderColor, fontSize, fontWeight } =
    window.getComputedStyle(selectedElement);

  console.log({
    backgroundColor,
    color,
    fontSize,
    fontWeight,
  });

  const backgroundPicker = document.createElement("input");
  backgroundPicker.type = "color";
  backgroundPicker.setAttribute("value", rgbToHex(backgroundColor));
  backgroundPicker.onchange = function () {
    selectedElement.style.backgroundColor = backgroundPicker.value;
  };

  let label = document.createElement("label");
  label.textContent = "Cor de fundo:";
  label.appendChild(backgroundPicker);
  settingsPanel.appendChild(label);

  const textColorPicker = document.createElement("input");
  textColorPicker.type = "color";
  textColorPicker.setAttribute("value", rgbToHex(color));
  textColorPicker.onchange = function () {
    selectedElement.style.color = textColorPicker.value;
  };

  label = document.createElement("label");
  label.textContent = "Cor de texto:";
  label.appendChild(textColorPicker);
  settingsPanel.appendChild(label);

  const borderColorPicker = document.createElement("input");
  borderColorPicker.type = "color";
  borderColorPicker.setAttribute("value", rgbToHex(borderColor));
  borderColorPicker.onchange = function () {
    selectedElement.style.borderColor = borderColorPicker.value;
  };

  label = document.createElement("label");
  label.textContent = "Cor da borda:";
  label.appendChild(borderColorPicker);
  settingsPanel.appendChild(label);

  const fontSizePicker = document.createElement("input");
  fontSizePicker.type = "number";
  fontSizePicker.setAttribute("value", parseFloat(fontSize));
  fontSizePicker.onchange = () => {
    selectedElement.style.fontSize =
      fontSizePicker.value +
      fontSize.slice(parseFloat(fontSize).toString().length);
  };

  label = document.createElement("label");
  label.textContent = "Tamanho da fonte:";
  label.appendChild(fontSizePicker);
  settingsPanel.appendChild(label);

  const fontWeightPicker = document.createElement("select");
  const options = [
    "100",
    "200",
    "300",
    "400",
    "500",
    "600",
    "700",
    "800",
    "900",
    "bold",
    "bolder",
    "lighter",
    "normal",
  ].map((weight) => {
    const option = document.createElement("option");
    option.value = weight;
    option.textContent = weight;

    return option;
  });
  fontWeightPicker.append(...options);
  fontWeightPicker.setAttribute("value", fontWeight);
  fontWeightPicker.onchange = () => {
    selectedElement.style.fontWeight = fontWeightPicker.value;
  };

  label = document.createElement("label");
  label.textContent = "Peso da fonte:";
  label.appendChild(fontWeightPicker);
  settingsPanel.appendChild(label);
}

/** @param {string} color */
function rgbToHex(color) {
  let match;

  if ((match = color.match(/\((.*)\)/))) {
    const [r, g, b] = match[1].split(",").map((s) => parseInt(s.trim(), 10));

    return "#" + [r, g, b].map((c) => c.toString(16).padStart(2, "0")).join("");
  }

  return undefined;
}
