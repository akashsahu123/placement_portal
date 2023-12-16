<?php
function createStdSidebar($active)
{
  $nav_titles = ['Home', 'Event Timings', 'Verify Students', 'Verify Companies'];
  $links = ['admin_home.php', 'admin_event_timings.php', 'admin_verify_students.php', 'admin_verify_companies.php'];

  echo '<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
          <symbol id="Home" viewBox="0 0 16 16">
            <title>Home</title>
            <path d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4.5a.5.5 0 0 0 .5-.5v-4h2v4a.5.5 0 0 0 .5.5H14a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146zM2.5 14V7.707l5.5-5.5 5.5 5.5V14H10v-4a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v4H2.5z"/>
          </symbol>
          <symbol id="Event Timings" viewBox="0 0 16 16">
            <title>Clock</title>
            <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
          </symbol>
          <symbol id="Verify Students" viewBox="0 0 16 16">
            <title>Verify</title>
            <path fill-rule="evenodd" d="M10.354 6.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7 8.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
            <path d="M4.406 3.342A5.53 5.53 0 0 1 8 2c2.69 0 4.923 2 5.166 4.579C14.758 6.804 16 8.137 16 9.773 16 11.569 14.502 13 12.687 13H3.781C1.708 13 0 11.366 0 9.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383zm.653.757c-.757.653-1.153 1.44-1.153 2.056v.448l-.445.049C2.064 6.805 1 7.952 1 9.318 1 10.785 2.23 12 3.781 12h8.906C13.98 12 15 10.988 15 9.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 4.825 10.328 3 8 3a4.53 4.53 0 0 0-2.941 1.1z"/>
          </symbol>
          <symbol id="Verify Companies" viewBox="0 0 16 16">
            <title>Verify</title>
            <path fill-rule="evenodd" d="M10.354 6.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7 8.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
            <path d="M4.406 3.342A5.53 5.53 0 0 1 8 2c2.69 0 4.923 2 5.166 4.579C14.758 6.804 16 8.137 16 9.773 16 11.569 14.502 13 12.687 13H3.781C1.708 13 0 11.366 0 9.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383zm.653.757c-.757.653-1.153 1.44-1.153 2.056v.448l-.445.049C2.064 6.805 1 7.952 1 9.318 1 10.785 2.23 12 3.781 12h8.906C13.98 12 15 10.988 15 9.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 4.825 10.328 3 8 3a4.53 4.53 0 0 0-2.941 1.1z"/>
          </symbol>
        </svg>';

  echo '<aside id="sidebar" class="d-flex flex-column flex-shrink-0 p-3 bg-white shadow" style="height: 100vh; width: 280px;">
        <button id="close-menu" class="d-none d-md-none bg-white btn position-absolute top-0 end-0 m-0"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16"><path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/></svg></button>
        <div class="text-center my-3 mb-md-0 link-dark">
          <span class="h5">Placement Portal</span>
        </div>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">';

  $n = count($nav_titles);
  $i = 0;

  for ($i = 0; $i < $n; ++$i) {
    $title = $nav_titles[$i];
    $link = $links[$i];

    if ($title == $active) {
      echo '
                <li class="nav-item">
                    <a href="#" class="nav-link active py-2 my-1 disabled">
                        <svg class="me-2 bi" fill="currentColor" width="16" height="16"><use xlink:href="#' . $title . '"></use></svg>
                        ' . $title . '
                    </a>
                </li>';
    } else {
      echo '
                <li class="nav-item">
                    <a href="' . $link . '" class="nav-link link-dark text-muted py-2 my-1" aria-current="page">
                    <svg class="me-2 bi" fill="currentColor" width="16" height="16"><use xlink:href="#' . $title . '"></use></svg>
                        ' . $title . '
                    </a>
                </li>';
    }
  }

  echo '</ul></aside>';
}
?>