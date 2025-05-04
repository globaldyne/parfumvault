// This script dynamically types out example prompts for a perfume AI assistant on the webpage.
// It cycles through a list of example prompts, typing each one character by character,
// and includes a blinking cursor effect for a terminal-like appearance.

document.addEventListener("DOMContentLoaded", function () {
	const examples = [
		"Create a fresh formula for a summer perfume",
		"Generate a woody masculine scent",
		"Suggest a floral blend with vanilla base",
		"Design a unisex fragrance for evening wear"
	];
	let exampleIndex = 0;
	let charIndex = 0;
	const terminalText = document.getElementById("terminalText");
	const cursor = document.querySelector(".blinking-cursor");

	function typeExample() {
		if (charIndex < examples[exampleIndex].length) {
			terminalText.textContent += examples[exampleIndex].charAt(charIndex);
			charIndex++;
			setTimeout(typeExample, 50);
		} else {
			setTimeout(() => {
				terminalText.textContent = "";
				charIndex = 0;
				exampleIndex = (exampleIndex + 1) % examples.length;
				typeExample();
			}, 2500);
		}
	}

	typeExample();

	// Optional blinking cursor effect
	setInterval(() => {
		cursor.style.visibility = cursor.style.visibility === "hidden" ? "visible" : "hidden";
	}, 500);
});
