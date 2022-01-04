Ext.onReady(function () {
    // Show/Hide a set of template variables
    ToggleTVSet.toggleTVSet = function (tvs, show) {
        var field;
        Ext.each(tvs, function (tv) {
            field = Ext.get('tv' + tv + '-tr');
            if (field) {
                if (show) {
                    field.setStyle('display', 'block');
                } else {
                    field.setStyle('display', 'none');
                }
            }
        });
        if (ToggleTVSet.options.debug) {
            console.log('ToggleTVSet triggered tvs(' + tvs + ') set to ' + ((show) ? 'show' : 'hide') + '.');
        }
    };

    // Toggle a set of template variables by the value of a TV
    ToggleTVSet.toggleTVSets = function (tv, toggletvid, init) {
        var hideTVs, showTVs;
        if (init) {
            hideTVs = tv.hideTVs[toggletvid];
            showTVs = tv.showTVs[toggletvid];
        } else {
            hideTVs = ToggleTVSet.options.hideTVs[toggletvid];//tv.store.data.keys.join().split(',');
            showTVs = ToggleTVSet.options.showOptionTvs[toggletvid][tv.selectedIndex].split(',');//tv.getValue().split(',');
        }

        ToggleTVSet.toggleTVSet(hideTVs, 0);
        ToggleTVSet.toggleTVSet(showTVs, 1);

        if (!init && ToggleTVSet.options.toggleTVsClearHidden) {
            var clearTVs = hideTVs.filter(function (el) {
                return showTVs.indexOf(el) === -1;
            });
            ToggleTVSet.clearTVSet(clearTVs);
        }
    };

    // Clear a set of template variables
    ToggleTVSet.clearTVSet = function (tvs) {
        Ext.each(tvs, function (tv) {
            var field = ToggleTVSet.options.resourceForm.findField('tv' + tv);
            if (field) {
                field.setValue('');
            }
        });
    };

    if (ToggleTVSet.options.debug) {
        Ext.util.Observable.capture(Ext.getCmp('modx-panel-resource'), function (e) {
            console.log(e, arguments);
        });
    }

    ToggleTVSet.options.resourcePanel = Ext.getCmp('modx-panel-resource');
    ToggleTVSet.options.resourceForm = ToggleTVSet.options.resourcePanel.getForm();
    ToggleTVSet.options.initialized = false;

    ToggleTVSet.options.resourcePanel.on('afterlayout', function () {
        if (!ToggleTVSet.options.initialized) {
            Ext.each(ToggleTVSet.options.toggleTVs, function (toggleTV) {
                ToggleTVSet.toggleTVSets(ToggleTVSet.options, toggleTV ,true);

                var field = ToggleTVSet.options.resourceForm.findField('tv' + toggleTV);
                if (field) {
                    field.on('select', function () {
                        ToggleTVSet.toggleTVSets(this, toggleTV, false);
                    });
                }
            });
        }
        ToggleTVSet.options.initialized = true;
    });
});
