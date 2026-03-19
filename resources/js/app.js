import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const bindPhilippineClock = () => {
	const clock = document.getElementById('ph-time');

	if (!clock) {
		return;
	}

	const dateFmt = new Intl.DateTimeFormat('en-US', {
		timeZone: 'Asia/Manila',
		month: 'long',
		day: 'numeric',
		year: 'numeric',
	});

	const timeFmt = new Intl.DateTimeFormat('en-US', {
		timeZone: 'Asia/Manila',
		hour: 'numeric',
		minute: '2-digit',
		hour12: true,
	});

	const renderClock = () => {
		const now = new Date();
		clock.textContent = `${dateFmt.format(now)} — ${timeFmt.format(now)} PHT`;
	};

	renderClock();
	setInterval(renderClock, 1000);
};

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', bindPhilippineClock, { once: true });
} else {
	bindPhilippineClock();
}
