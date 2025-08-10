require('./bootstrap');

// Auto-require all colocated page scripts named *.page.js under resources/views.
try {
	const pagesContext = require.context('../views', true, /\\.page\\.js$/);
	pagesContext.keys().forEach(pagesContext);
} catch (e) {
	console.warn('Page context load failed (non-webpack env?)', e);
}

// Generic page initializer dispatcher using a <meta name="page-id" content="..."> set in each Blade.
function runPageInit() {
	const meta = document.querySelector('meta[name="page-id"]');
	if (!meta) return;
	const pageId = meta.getAttribute('content');
	if (window.__PageInits && typeof window.__PageInits[pageId] === 'function') {
		try {
			window.__PageInits[pageId]();
		} catch (e) {
			console.error('Error running page init for', pageId, e);
		}
	}
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', runPageInit);
} else {
	runPageInit();
}
