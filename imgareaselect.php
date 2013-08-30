    <link rel="stylesheet" type="text/css" href="imgareaselect/css/imgareaselect-default.css" />
    <script type="text/javascript" src="script/jquery.js"></script>
    <script type="text/javascript" src="imgareaselect/scripts/jquery.imgareaselect.pack.js"></script>
    <script type="text/javascript">
      var xcrop = null, ycrop = null, wcrop = null, hcrop = null;
      function selectEnd(obj, state) {
        if (xcrop == null) {
          xcrop = document.getElementById("xcrop");
          ycrop = document.getElementById("ycrop");
          wcrop = document.getElementById("wcrop");
          hcrop = document.getElementById("hcrop");
        }
        xcrop.value = state.x1;
        ycrop.value = state.y1;
        wcrop.value = state.width;
        hcrop.value = state.height;
      }
      $(document).ready(function () {
        $('img#photo').imgAreaSelect({
          x1: 0, y1: 0, x2: 154, y2: 96,
          minWidth: 154, minHeight: 96,
          aspectRatio: '154:96',
          handles: true,
          onSelectChange: selectEnd
        });
      });
    </script>
    <img id="photo" src="images/defcrop.png" alt="Image Crop" />
