    <link rel="stylesheet" type="text/css" href="croppie/croppie.css" />
    <script type="text/javascript" src="croppie/croppie.min.js"></script>
    <script type="text/javascript">
      var xcrop = null, ycrop = null, wcrop = null, hcrop = null;
      function selectChange(ev) {
        if (xcrop == null) {
          xcrop = document.getElementById("xcrop");
          ycrop = document.getElementById("ycrop");
          wcrop = document.getElementById("wcrop");
          hcrop = document.getElementById("hcrop");
        }
        xcrop.value = ev.points[0];
        ycrop.value = ev.points[1];
        wcrop.value = ev.points[2] - ev.points[0];
        hcrop.value = ev.points[3] - ev.points[1];
      }
      let started = false;
      function start_designer() {
        if (started) return;
        started = true;
        $('img#photo').croppie({
          viewport: {
            width: 154,
            height: 96,
          },
          boundary: {
            width: 512,
            height: 320,
          },
          update: selectChange,
        });
      }
      function rebind_cropper(url) {
        $('img#photo').croppie('bind', {
          url: url
        });
      }
    </script>
    <div><img id="photo" src="images/defcrop.png" alt="Image Crop" /></div>
