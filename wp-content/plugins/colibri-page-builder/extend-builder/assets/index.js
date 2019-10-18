(function($) {
    window.CP_Customizer.addModule(function(CP_Customizer) {
        CP_Customizer.hooks.addFilter('set_content_value_promise', function(save, options) {
            var promise = hop.exportToOption(save ? undefined : {
                html: false,
                css: false
            }, options)
            //console.error('set value', promise);
            return promise;
        });

        function partialId(name) {
            return "extend-builder-" + name + "-json";
        }
        function areaSelector(name) {
            return "#" + partialId(name);
        }

        function mountPreview(partials) {
            var previewW = CP_Customizer.preview.frame();
            var mountPoints = {};
            Object.keys(partials).forEach(function(name) {
                var el = previewW.jQuery(areaSelector(name));
                //console.error('selector', name, areaSelector(name), el);
                if (el.length) {
                    mountPoints[name] = el.parent()[0];
                }
            });
            hop.iframeHop.mount(mountPoints);
        }

        function decodeJson(partial) {
            partial.json = _.isString(partial.json)? JSON.parse(partial.json) : partial.json;
            return partial;
        }

        function getPreviewURL() {
          var urlParts = _wpCustomizeSettings.url.preview.split('?');
          var query = urlParts.length > 1 ? urlParts[1] : "";
          query = query.split('&');
          if (wp.customize.settings.changeset.uuid) {
            query.push( "customize_changeset_uuid=" + wp.customize.settings.changeset.uuid);
            query = "?" + query.join('&')
          }
          return urlParts[0] + query;
        }
        function initBuilder() {
            var hopInit = CP_Customizer.preview.frame().colibriInit;
            if (!hopInit) return;
            $('#accordion-section-general_site_typography, #accordion-section-general_site_typography > *').off('click')
            $('#accordion-section-general_site_typography').on("click", function(event) {
                top.hop.sidebar.selectTypography();
            });
            $('#accordion-section-general_site_colors, #accordion-section-general_site_colors > *').off('click')
            $('#accordion-section-general_site_colors').on("click", function(event) {
                top.hop.sidebar.selectColors();
            });
            $('#accordion-section-general_site_spacing, #accordion-section-general_site_spacing > *').off('click')
            $('#accordion-section-general_site_spacing').on("click", function(event) {
                top.hop.sidebar.selectSpacing();
            });
            $('#accordion-section-general_site_effects, #accordion-section-general_site_effects > *').off('click')
            $('#accordion-section-general_site_effects').on("click", function(event) {
                top.hop.sidebar.selectEffects();
            });
            CP_Customizer.unbind('PREVIEW_LOADED', initBuilder);
            //CP_Customizer.bind('PREVIEW_LOADED', mountPreview);
            // append sidebar to the customizer root//
            var extendSidebar = jQuery("<div class='extend-builder-sidebar'></div>");
            jQuery('#customize-controls .wp-full-overlay-sidebar-content').append(extendSidebar);

            var partials = {};
            for (var partialName in hopInit.page) {
                if (hopInit.page.hasOwnProperty(partialName)) {
                    partials[partialName] = decodeJson(hopInit.page[partialName])
                }
            }
            CP_Customizer.bind('PREVIEW_LOADED', function() {
                mountPreview(partials);
            });
            var pages = [{
                id: hopInit.ID,
                partials: partials
            }]
            //console.error('pages', pages);
            var api = {
                call: function(action, options, success, error) {
                    jQuery.post(ajaxurl, {
                        action: 'extend_builder',
                        api: JSON.stringify({
                            action: action,
                            data: options
                        }),
                    }, function(response) {
                        //console.error('call response###', response);
                        success(response);
                    });
                },
                postsTypes: {
                    list: function(data, cb) {
                        api.call('postsTypes/list', data, cb);
                    }
                }
            }
            var init = {
                sidebar: {
                    selector: ".extend-builder-sidebar"
                },
                sections: {
                    selector: "#extend-builder-sections"
                },
                data: {
                    theme: hopInit.meta.theme,
                    pages: pages,
                    page: {
                        data: hopInit.data
                    }
                },
                service: {
                    api: api,
                    rest : CP_Customizer.IO.rest,
                    cachedCalls: {
                        shortcodes: {},
                    },
                    invalidateCache: function () {
                        this.cachedCalls = {
                            shortcodes: {}
                        };
                    },
                    listPageTypes: function (success, error) {
                        api.call('list_page_types', {}, success, error);
                    },
                    openMediaBrowser: function(options) {
                        CP_Customizer.openMediaBrowser(options.type, function(src) {
                            options.callback(src);
                        }, options.data || {});
                    },
                    renderShortcode: function(options) {
                        if (options.callback && options.shortcode) {
                            var context = {
                                query: CP_Customizer.preview.data().queryVars,
                                main_query: CP_Customizer.preview.data().mainQueryVars,
                            }
                            var encodedShortcode = btoa(encodeURIComponent(options.shortcode));
                            var currentChangeset = CP_Customizer.utils.deepClone(wp.customize.previewer.query());

                            // look for a cached shortcode
                            if (this.cachedCalls.shortcodes[encodedShortcode]) {

                                if (options.invalidateCache) {
                                    this.cachedCalls.shortcodes[encodedShortcode] = undefined;
                                } else {
                                    console.log('Cached Shortcode Hit:', options.shortcode)
                                    options.callback({
                                        html: this.cachedCalls.shortcodes[encodedShortcode]
                                    });
                                    return;
                                }
                            }

                            var self = this;
                            var data = _.extend( _.omit(currentChangeset,['customized']), {
                                action: 'cp_shortcode_refresh',
                                shortcode: encodedShortcode,
                                rawShortcode : options.shortcode,
                                context: context,
                                _: Date.now()
                            });
                            jQuery.ajax({
                                url: ajaxurl,
                                method: 'POST',
                                data: data
                            }).done(function (response) {
                                if (response != -1) {
                                    if (options.cache) {
                                        self.cachedCalls.shortcodes[encodedShortcode] = response;
                                    }
                                    options.callback({
                                        html: response
                                    });
                                }
                            });
                        }
                    }
                }
            }
            var preview = CP_Customizer.preview;
            init.service.invalidateCache();
            hop.init(init)
            hop.sidebar.$on("change", function() {
                CP_Customizer.updateState();
            });
           
            top.wp.customize.previewedDevice.bind(function(device) {
                console.error('device', device);
                hop.sidebar.$store.dispatch("ui/selectedMedia", device);
            });
            hop.sidebar.$on("device", function(device) {
                console.error('device', device);
                top.wp.customize.previewedDevice.set(device);
            });
            mountPreview(partials);
        }
        CP_Customizer.bind('PREVIEW_LOADED', initBuilder);
    });
})(jQuery, window);
