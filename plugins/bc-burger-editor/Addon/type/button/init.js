'use strict';
BgE.registerTypeModule('Button', {
	migrate: (type) => {
		const data = type.export();
		if (BgE.versionCheck.lt(type.version, '2.13.0')) {
			if (data.type) {
				data.kind = data.type.replace(/^bgt-btn--/, '');
				delete data.type;
			}
			if (!data.kind) {
				data.kind = 'link';
			}
		}
		return data;
	},
});
