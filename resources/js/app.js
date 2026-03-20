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

const bindProfileImageUploader = () => {
	const container = document.getElementById('profile-image-uploader');

	if (!container) {
		return;
	}

	const fileInput = document.getElementById('profile-image-input');
	const uploadButton = document.getElementById('profile-image-upload-btn');
	const removeButton = document.getElementById('profile-image-remove-btn');
	const loadingText = document.getElementById('profile-image-loading');
	const errorText = document.getElementById('profile-image-error');

	if (!fileInput || !uploadButton || !removeButton || !loadingText || !errorText) {
		return;
	}

	const uploadUrl = container.dataset.uploadUrl;
	const removeUrl = container.dataset.removeUrl;
	const maxSize = Number(container.dataset.maxSize || 2 * 1024 * 1024);
	let hasImage = container.dataset.hasImage === '1';

	if (!uploadUrl || !removeUrl) {
		return;
	}

	let selectedFile = null;
	let previewUrl = null;
	const fallbackAvatar = '/images/default-avatar.svg';

	const bindAvatarFallback = () => {
		document.querySelectorAll('[data-avatar-image]').forEach((img) => {
			if (img.dataset.fallbackBound === '1') {
				return;
			}

			img.dataset.fallbackBound = '1';
			img.addEventListener('error', () => {
				const fallback = img.dataset.avatarFallback || fallbackAvatar;
				if (img.src !== fallback) {
					img.src = fallback;
				}
			}, { once: true });
		});
	};

	const updateAvatarEverywhere = (url) => {
		document.querySelectorAll('[data-avatar-image]').forEach((img) => {
			img.src = url || img.dataset.avatarFallback || fallbackAvatar;
		});
		bindAvatarFallback();
	};

	const setError = (message = '') => {
		errorText.textContent = message;
		errorText.classList.toggle('hidden', message.length === 0);
	};

	const setLoading = (isLoading) => {
		loadingText.classList.toggle('hidden', !isLoading || loadingText.textContent.length === 0);
		uploadButton.disabled = isLoading || !selectedFile;
		removeButton.disabled = isLoading || !hasImage;
		fileInput.disabled = isLoading;
	};

	const setHasImage = (value) => {
		hasImage = value;
		container.dataset.hasImage = value ? '1' : '0';
		removeButton.disabled = !value;
	};

	const optimizeSquareImage = async (file) => {
		const bitmap = await createImageBitmap(file);

		const sourceSize = Math.min(bitmap.width, bitmap.height);
		const sx = Math.floor((bitmap.width - sourceSize) / 2);
		const sy = Math.floor((bitmap.height - sourceSize) / 2);
		const targetSize = 512;

		const canvas = document.createElement('canvas');
		canvas.width = targetSize;
		canvas.height = targetSize;

		const ctx = canvas.getContext('2d');

		if (!ctx) {
			bitmap.close();
			throw new Error('Could not process image.');
		}

		ctx.drawImage(bitmap, sx, sy, sourceSize, sourceSize, 0, 0, targetSize, targetSize);
		bitmap.close();

		const preferredType = 'image/webp';
		const blob = await new Promise((resolve) => {
			canvas.toBlob(resolve, preferredType, 0.85);
		});

		if (!blob) {
			throw new Error('Could not optimize image.');
		}

		if (blob.size > maxSize) {
			throw new Error('Optimized image is still larger than 2MB. Please choose another image.');
		}

		return new File([blob], `avatar-${Date.now()}.webp`, { type: preferredType });
	};

	fileInput.addEventListener('change', async () => {
		setError('');
		selectedFile = null;
		uploadButton.disabled = true;

		const file = fileInput.files?.[0];

		if (!file) {
			return;
		}

		const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

		if (!allowedTypes.includes(file.type)) {
			setError('Please choose a JPEG, PNG, or WebP file.');
			return;
		}

		if (file.size > maxSize) {
			setError('Image must be 2MB or less.');
			return;
		}

		try {
			selectedFile = await optimizeSquareImage(file);

			if (previewUrl) {
				URL.revokeObjectURL(previewUrl);
			}

			previewUrl = URL.createObjectURL(selectedFile);
			updateAvatarEverywhere(previewUrl);
			uploadButton.disabled = false;
		} catch (error) {
			setError(error instanceof Error ? error.message : 'Failed to prepare the selected image.');
		}
	});

	bindAvatarFallback();
	setHasImage(hasImage);

	uploadButton.addEventListener('click', async () => {
		if (!selectedFile) {
			return;
		}

		setError('');
		loadingText.textContent = 'Uploading image...';
		setLoading(true);

		try {
			const formData = new FormData();
			formData.append('image', selectedFile);

			const response = await window.axios.post(uploadUrl, formData, {
				headers: {
					'Content-Type': 'multipart/form-data',
				},
			});

			const imageUrl = response?.data?.data?.profile_image_url;

			if (imageUrl) {
				updateAvatarEverywhere(imageUrl);
				setHasImage(true);
			}

			selectedFile = null;
			fileInput.value = '';
			uploadButton.disabled = true;
		} catch (error) {
			setError(error?.response?.data?.message || 'Failed to upload image. Please try again.');
		} finally {
			loadingText.textContent = '';
			setLoading(false);
		}
	});

	removeButton.addEventListener('click', async () => {
		if (!hasImage) {
			return;
		}

		setError('');
		loadingText.textContent = 'Removing image...';
		setLoading(true);

		try {
			const response = await window.axios.delete(removeUrl);
			const imageUrl = response?.data?.data?.profile_image_url;

			updateAvatarEverywhere(imageUrl);
			setHasImage(false);
			selectedFile = null;
			fileInput.value = '';
			uploadButton.disabled = true;
		} catch (error) {
			setError(error?.response?.data?.message || 'Failed to remove image. Please try again.');
		} finally {
			loadingText.textContent = '';
			setLoading(false);
		}
	});
};

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', bindProfileImageUploader, { once: true });
} else {
	bindProfileImageUploader();
}
