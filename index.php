<!DOCTYPE html>

<!-- Don't let search engines index the page while it's in development -->
<meta name="robots" content="noindex" />

<!-- Prepare a manafest link. Leaving the href empty allows us to 
      choose which web app manifest we want the page to have at load time -->
<link rel="manifest" />

<!-- Set the viewport (helps fix scaling on mobile devices) -->
<meta name="viewport" content="width=device-width,initial-scale=0.8" />

<!-- Prepare generic body and html that will apply to every page of every app.
     Any per-page or per-component style is encapsulated in the shadow DOM of the body. -->
<style>
 html,
 body {
  /* Disable the default behavior of many user agents that allows the user to scroll elements 'past' their edge */
  overscroll-behavior-y: contain !important;

  /* Prevent the appearance of any scroll bars which can't be adjusted from inside the shadow element. */
  overflow: clip;

  /* Terminate overflowing text with '...' */
  /* text-overflow: ellipsis; */

  /* Prevent word-wrap to allow the ellipsis overflow behavior. */
  /* white-space: nowrap; */

  /* Make the root view always full height */
  height: 100%;

  /* Remove the default user agent margin */
  margin: 0;

  /* Prevent text selection */
  -webkit-user-select: none;
  -ms-user-select: none;
  user-select: none;
 }
</style>

<!-- Load the script that will render the rest of the page. -->
<script src="https://<?= $_SERVER['HTTP_HOST'] ?>/live.js"></script>