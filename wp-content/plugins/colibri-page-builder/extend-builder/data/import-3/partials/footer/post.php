<?php namespace ExtendBuilder; load_file_value('partial_data', array (
  'partial' => 
  array (
    'id' => 11,
    'name' => 'footer_post',
    'type' => 'footer',
    'data' => 
    array (
      'css' => NULL,
      'json' => '{"name":"hop-footer","children":[{"name":"hop-section","props":{"anchor":false,"name":"Copyright","attrs":{"id":"copyright"}},"children":[{"name":"hop-row","children":[{"name":"hop-column","style":{"descendants":{"outer":{"columnWidth":{"type":"custom","custom":{"value":100,"unit":"%"}}}}},"children":[{"name":"hop-copyright","slot":null,"parentId":"11-f4","index":1,"id":"11-f5","partialId":11,"styleRef":73,"type":"footer"}],"id":"11-f4","parentId":"11-f3","partialId":11,"styleRef":72,"type":"footer"}],"parentId":"11-f2","index":0,"id":"11-f3","partialId":11,"styleRef":71,"type":"footer"}],"id":"11-f2","parentId":"11-f1","partialId":11,"styleRef":70,"type":"footer"}],"id":"11-f1","ui":{"isSelected":false,"isHovered":false},"type":"footer","partialId":11,"styleRef":69}',
      'meta' => 
      array (
        'styleRefs' => 
        array (
          0 => 69,
          1 => 70,
          2 => 71,
          3 => 72,
          4 => 73,
        ),
      ),
      'html' => '<div data-colibri-component="footer-parallax" data-colibri-id="11-f1" class="page-footer style-69 style-local-11-f1 position-relative">
  <!---->
  <div data-colibri-component="section" data-colibri-id="11-f2" id="copyright" class="h-section h-section-global-spacing d-flex align-items-lg-center align-items-md-center align-items-center style-70 style-local-11-f2 position-relative">
    <!---->
    <!---->
    <div class="h-section-grid-container h-section-boxed-container">
      <!---->
      <div data-colibri-id="11-f3" class="h-row-container gutters-row-lg-1 gutters-row-md-1 gutters-row-2 gutters-row-v-lg-1 gutters-row-v-md-1 gutters-row-v-2 style-71 style-local-11-f3 position-relative">
        <!---->
        <div class="h-row justify-content-lg-center justify-content-md-center justify-content-center align-items-lg-stretch align-items-md-stretch align-items-stretch gutters-col-lg-1 gutters-col-md-1 gutters-col-2 gutters-col-v-lg-1 gutters-col-v-md-1 gutters-col-v-2">
          <!---->
          <div class="h-column h-column-container d-flex h-col-lg-auto h-col-md-auto h-col-auto style-72-outer style-local-11-f4-outer">
            <div data-colibri-id="11-f4" class="d-flex h-flex-basis h-column__inner h-px-lg-1 h-px-md-1 h-px-2 v-inner-lg-1 v-inner-md-1 v-inner-2 style-72 style-local-11-f4 position-relative">
              <!---->
              <!---->
              <div class="w-100 h-y-container h-column__content h-column__v-align flex-basis-100 align-self-lg-start align-self-md-start align-self-start">
                <!---->
                <div data-colibri-id="11-f5" class="style-73 style-local-11-f5 position-relative h-element">
                  <!---->
                  <div class="h-global-transition-all">[colibri_copyright]© {year} {site-name}. Created for free using WordPress and
                    <a href="https://colibriwp.com">Colibri</a>[/colibri_copyright]</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>',
      'id' => 11,
      'lang' => 'default',
      'dynamic' => false,
    ),
  ),
  'partialCss' => 
  array (
    'local-11-f4' => 
    array (
      'desktop' => '#colibri .style-local-11-f4-outer {
  width: 100% ;
  flex: 0 0 auto;
  -ms-flex: 0 0 auto;
}
',
      'tablet' => '#colibri .style-local-11-f4-outer {
  width: 100% ;
  flex: 0 0 auto;
  -ms-flex: 0 0 auto;
}
',
      'mobile' => '#colibri .style-local-11-f4-outer {
  width: 100% ;
  flex: 0 0 auto;
  -ms-flex: 0 0 auto;
}
',
    ),
  ),
  'styleRefs' => 
  array (
    0 => 69,
    1 => 70,
    2 => 71,
    3 => 72,
    4 => 73,
  ),
  'rules' => 
  array (
    0 => 
    array (
      'id' => 69,
      'type' => 'hop-footer',
      'props' => 
      array (
        'useFooterParallax' => true,
      ),
      'v' => '1.1',
      'nodes' => 
      array (
        0 => '11-f1',
      ),
    ),
    1 => 
    array (
      'id' => 70,
      'type' => 'hop-section',
      'style' => 
      array (
        'padding' => 
        array (
          'top' => 
          array (
            'path' => 'value',
            'value' => '30',
          ),
          'bottom' => 
          array (
            'path' => 'value',
            'value' => '30',
          ),
        ),
      ),
      'v' => '1.1',
      'nodes' => 
      array (
        0 => '11-f2',
      ),
    ),
    2 => 
    array (
      'id' => 71,
      'props' => 
      array (
        'layout' => 
        array (
          'equalWidth' => false,
          'horizontalInnerGap' => 1,
          'verticalGap' => 1,
          'horizontalGap' => 1,
        ),
        'media' => 
        array (
          'mobile' => 
          array (
            'layout' => 
            array (
              'verticalGap' => 2,
              'horizontalGap' => 2,
            ),
          ),
        ),
      ),
      'type' => 'hop-row',
      'v' => '1.1',
      'nodes' => 
      array (
        0 => '11-f3',
      ),
    ),
    3 => 
    array (
      'id' => 72,
      'type' => 'hop-column',
      'props' => 
      array (
        'layout' => 
        array (
          'vSpace' => 
          array (
            'value' => '0',
          ),
          'horizontalInnerGap' => 1,
          'verticalInnerGap' => 1,
        ),
        'media' => 
        array (
          'mobile' => 
          array (
            'layout' => 
            array (
              'horizontalInnerGap' => 2,
              'verticalInnerGap' => 2,
            ),
          ),
        ),
      ),
      'style' => 
      array (
        'padding' => 
        array (
          'left' => 
          array (
            'unit' => 'px',
          ),
          'right' => 
          array (
            'unit' => 'px',
          ),
          'top' => 
          array (
            'unit' => 'px',
          ),
          'bottom' => 
          array (
            'unit' => 'px',
          ),
        ),
        'media' => 
        array (
          'mobile' => 
          array (
            'padding' => 
            array (
              'left' => 
              array (
                'unit' => 'px',
              ),
              'right' => 
              array (
                'unit' => 'px',
              ),
              'top' => 
              array (
                'unit' => 'px',
              ),
              'bottom' => 
              array (
                'unit' => 'px',
              ),
            ),
          ),
        ),
        'descendants' => 
        array (
          'outer' => 
          array (
            'media' => 
            array (
              'mobile' => 
              array (
                'padding' => 
                array (
                  'top' => 
                  array (
                    'unit' => 'px',
                  ),
                  'bottom' => 
                  array (
                    'unit' => 'px',
                  ),
                  'left' => 
                  array (
                    'unit' => 'px',
                  ),
                  'right' => 
                  array (
                    'unit' => 'px',
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
      'v' => '1.1',
      'nodes' => 
      array (
        0 => '11-f4',
      ),
    ),
    4 => 
    array (
      'id' => 73,
      'type' => 'hop-copyright',
      'nodeId' => '13-f6',
      'v' => '1.1',
      'nodes' => 
      array (
        0 => '11-f5',
      ),
    ),
  ),
  'cssById' => 
  array (
    70 => 
    array (
      'desktop' => '#colibri .style-70 {
  height: auto;
  min-height: unset;
  padding-top: 30px;
  padding-bottom: 30px;
}
',
      'tablet' => '#colibri .style-70 {
  height: auto;
  min-height: unset;
  padding-top: 30px;
  padding-bottom: 30px;
}
',
      'mobile' => '#colibri .style-70 {
  height: auto;
  min-height: unset;
  padding-top: 30px;
  padding-bottom: 30px;
}
',
    ),
    72 => 
    array (
      'desktop' => '#colibri .style-72 {
  text-align: center;
  height: auto;
  min-height: unset;
}
',
      'tablet' => '#colibri .style-72 {
  text-align: center;
  height: auto;
  min-height: unset;
}
',
      'mobile' => '#colibri .style-72 {
  text-align: center;
  height: auto;
  min-height: unset;
}
',
    ),
  ),
));
