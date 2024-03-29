self.addEventListener('push', function(event) {
	if (!(self.Notification && self.Notification.permission === 'granted')) {
		return;
	}

	const buildNotification = (body) => {
		const payload = JSON.parse(body);

		const options = {
			badge: payload.badge,
			icon: payload.icon || null,
			body: payload.content,
			image: payload.image || null,
			data: [],
			actions: []
		};

		if (payload.url) {
			options.data.push({
				url: payload.url
			});
		}

		if (payload.actions && 'actions' in Notification.prototype) {
			payload.actions.forEach((action) => {
				options.actions.push({
					action: action.name,
					title: action.title
				});
			});
		}

		return self.registration.showNotification(payload.title, options);
	};

	if (event.data) {
		const message = event.data.text();
		event.waitUntil(buildNotification(message));
	}
});

self.addEventListener('notificationclick', function(event) {
	event.notification.close();
	event.waitUntil(clients.openWindow(event.notification.data[0].url));
});