<? $Δ = [
 "https://core.parts/components/button-/construct.js" => '
  (url, layout = "", manifest = "", onclickjs = `()=>{ console.info("button click ${url}") }`) => {
   let parts = ("" + Ω["https://core.parts/components/button-/construct.json"]).replace(/\$1/g, url).replace(/\$2/, layout.replace(/\n/g, "\\\\n").replace(/"/g, \'\\\\"\')).replace(/\$3/, manifest).replace(/"\$4"/, JSON.stringify(""+onclickjs))
   Object.entries(JSON.parse(parts)).forEach(([url, value]) => Ω[url] = value)
  }',
 "https://core.parts/components/button-/construct.json" => '
  {
   "$1layout.css?constructor": "$1layout.css.c.js",
   "$1layout.css.c.js": "{ return `:host { background-color: #c3c3c3;${(``+down) === `1` ? `background-image: linear-gradient(45deg, white 25%, transparent 25%, transparent 75%, white 75%, white), linear-gradient(45deg, white 25%, transparent 25%, transparent 75%, white 75%, white); background-size: 2px 2px; background-position: 0 0, 1px 1px;`:``}; box-sizing: border-box; box-shadow: ${(``+down) === `1` ? `inset -1px -1px white, inset 1px 1px black, inset -2px -2px #dbdbdb, inset 2px 2px #7a7a7a` : `inset -1px -1px black, inset 1px 1px white, inset -2px -2px #7a7a7a, inset 2px 2px #dbdbdb`}} $2` }",
   "$1manifest.uri": "$3",
   "$1onclick.js": "$4",
   "$1?layout": "$1layout.css",
   "$1?manifest": "$1manifest.uri",
   "$1?onclick": "$1onclick.js",
   "$1layout.css?down": "$1down.txt",
   "$1?onpointerdown": "$1onpointerdown.js",
   "$1onpointerdown.js": "e => { e.stopPropagation(); Ω[\'$1down.txt\'] = \'1\'; Ω[\'https://core.parts/behaviors/release/src.uri\'] = \'$1release.js\' }","$1release.js":"e => { Ω[\'$1down.txt\'] = \'0\' }",
   "$1down.txt": "0",
   "$1down.txt?fx": "$1down-fx.uri",
   "$1down-fx.uri": "$1layout.css"
  }',
 "https://core.parts/components/cell-/construct.js" => '
  (url, layout, manifest, layout_by_reference = false, manifest_by_reference = false) => {
   if (layout) {
    if (layout_by_reference) Ω[url + "?layout"] = layout
    else {
     const layout_url = url + "layout.css";
     Ω[url + "?layout"] = layout_url
     Ω[layout_url] = layout
    }
   }
   if (manifest) {
    if (manifest_by_reference) Ω[url + "?manifest"] = manifest
    else {
     const manifest_url = url + "manifest.uri";
     Ω[url + "?manifest"] = manifest_url
     Ω[manifest_url] = manifest
    }
   }
  }',
 "https://core.parts/components/click-/construct.json" => '
  {
   "$1?onclick": "$1onclick.js",
   "$1onclick.js": "e => Ω[\'https://core.parts/components/click-/handler.js\'](e, \'$2\',\'$3\')"
  }',
 "https://core.parts/components/click-/construct.js" => '
  ($1, $2 = "https://core.parts/components/click-/single-click.js", $3 = "https://core.parts/components/click-/double-click.js") => {
   return Ω["https://core.parts/components/click-/instantiate.js"]("click-", $1, $2, $3)
  }',
 "https://core.parts/components/click-/double-click.js" => '({ target }) => { console.info("double click", target) }',
 "https://core.parts/components/click-/handler.js" => '
  (e, $2, $3) => {
   const
    key = "%double_click_timer%",
    waiting = key in globalThis,
    own_url = e.target.url;
   if (waiting) {
    const
     config = globalThis[key],
     clicked_url = config.url;
    // Stop waiting for double click.
    clearTimeout(config.timeout)
    delete globalThis[key]
    if (clicked_url === own_url) {
     // Handle double click.
     return Ω[$3](e)
    }
   }
   // Wait for double click.
   globalThis[key] = { url: e.target.url, timeout: setTimeout(() => delete globalThis[key], 500) }
   // Handle single click.
   return Ω[$2](e)
  }',
 "https://core.parts/components/click-/single-click.js" => '({ target }) => { console.info("single click", target) }',
 "https://core.parts/components/click-/instantiate.js" => '
  (template_name, ...$) => Object.assign(Δ, JSON.parse($.reduce(
   (x, $n, n) => x.replace(new RegExp("\\\\$" + (n+1), "g"), $n),
   "" + Ω[`https://core.parts/components/${template_name}/construct.json`]
  )))',
 "https://core.parts/components/transform-/construct.js" => <<<JS
  (transform_url, position_url, directions = "nesw", move_handle_url) => {
   if (!/^n?e?s?w?$/.test(directions)) throw new TypeError(`transform component requires format /^n?e?s?w?$/ (got \${directions})`)
   const
    manifest = [],
    core_url = transform_url + "core-/",
    behavior_url = "https://core.parts/behaviors/resize/",
    resize_cell = dir => {
     const
      dir_url = transform_url + dir + "/",
      dir_base = `\${behavior_url}\${dir}.`;
     Ω[dir_url + "?layout"] = dir_base + "css"
     Ω[dir_url + "?onpointerdown"] = dir_url + "onpointerdown.js"
     Ω[dir_url + "onpointerdown.js?core"] = core_url + "onpointerdown.js"
     Ω[dir_url + "onpointerdown.js?mode"] = dir_base + "txt"
     manifest.push(dir_url)
    };
   Ω[core_url + "onpointerdown.js?core"] = behavior_url + "onpointerdown.js"
   Ω[core_url + "onpointerdown.js?position"] = position_url
   if (directions.includes("n")) {
    resize_cell("top-")
    if (directions.includes("e")) resize_cell("top-right")
    if (directions.includes("w")) resize_cell("top-left")
   }
   if (directions.includes("s")) {
    resize_cell("bottom-")
    if (directions.includes("e")) resize_cell("bottom-right")
    if (directions.includes("w")) resize_cell("bottom-left")
   }
   if (directions.includes("e")) resize_cell("right-")
   if (directions.includes("w")) resize_cell("left-")
   if (move_handle_url) {
    Ω[move_handle_url + "?onpointerdown"] = move_handle_url + "onpointerdown.js"
    Ω[move_handle_url + "onpointerdown.js?core"] = core_url + "onpointerdown.js"
    Ω[move_handle_url + "onpointerdown.js?mode"] = "https://core.parts/behaviors/move.txt"
   }
   return manifest.join(" ")
  }
  JS,
 "https://core.parts/php/index.php" => '<?
  $base = "https://core.parts/php";
  $Δ = array_merge($Δ, [
   "$base/strap.js" => $script = \'var causality = {}, onfetch = (Ω = new Proxy({}, new Proxy($1, { get: (Δ, Υ) => eval(Δ[V = "https://core.parts/proxy/alpha.js"]) })))["https://core.parts/file.js"]; onmessage = Ω["https://core.parts/client-to-server.js"]\',
   "$base/maintenance.txt" => "" . ($maintenance = true),
   "$base/version.txt" => "0.023",
   "$base/user.txt" => $user = $_SERVER["REMOTE_ADDR"],
   "$base/user.txt?fx" => "$base/username-fx.uri",
   "$base/users.json" => json_encode($users = ["eric house" => "35.138.226.122", "eric library" => "97.76.210.20", "jason" => "68.103.68.155"]),
   "$base/usernames.json" => json_encode(["35.138.226.122" => "eric house", "97.76.210.20" => "eric library", "99.108.88.76" => "jason"]),
   "$base/usernames.txt?fx" => "$base/username-fx.uri",
   "$base/registered.txt" => "" . ($registered = in_array($user, $users)),
   "$base/registered.txt?fx" => "$base/username-fx.uri",
   "$base/show_everything.txt" => "" . ($show_everything = $registered || !$maintenance),
   "$base/installer.html" => \'
     <script>
      var run = false;
      ((a, f) => a ? (a.addEventListener("message", _ => location.reload()), b => b ? f(b) : a.register(`https://${location.hostname}/everything.js`).then(({
       waiting: x,
       installing: y,
       active: z
      }) => {
       (x || y)?.addEventListener("statechange", ({
        target: t
       }) => t.state === "activated" ? f(t) : null);
       f(z)
      }))(a.controller) : console.error("!sw"))(navigator.serviceWorker, () => {
       if (run) return;
       run = true;
       location.reload?.()
      })
     </script>$ > core.parts<br>$ > installing...<br>$ > protocol \' . $_SERVER["SERVER_PROTOCOL"]
  ]);
  if ($_SERVER["REQUEST_URI"] === "/everything.js" && $show_everything) {
   header("content-type:text/javascript");
   echo str_replace("$1", json_encode($Δ, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_LINE_TERMINATORS | JSON_PRETTY_PRINT), $script);
  } else {
   echo $Δ["$base/boilerplate.html"];
   echo $Δ["$base/" . ($show_everything ? "installer.html" : "maintenance.html")];
  }
  ?>',
 "https://core.parts/php/maintenance.html" => '$ > closed for maintenance',
 "https://core.parts/php/boilerplate.html" => '
  <!DOCTYPE html>
  <meta name=robots content=noindex>
  <style>
   body {
    background: #222334;
    color: white;
    font: bold 11px / 16px monospace;
    white-space: pre;
   }
  </style>',
 "https://core.parts/php/username.txt?constructor" => 'https://core.parts/php/username.txt.c.js',
 "https://core.parts/php/username.txt.c.js" => 'return (""+registered === "1") ? JSON.parse(""+usernames)[user] : "guest"',
 "https://core.parts/php/username-fx.uri" => 'https://core.parts/php/username.txt',
 "https://core.parts/php/username.txt?registered" => 'https://core.parts/registered.txt',
 "https://core.parts/php/username.txt?usernames" => 'https://core.parts/php/usernames.json',
 "https://core.parts/php/username.txt?user" => 'https://core.parts/user.txt',
 "https://core.parts/php/everything.php?constructor" => 'https://core.parts/php/everything.php.c.js',
 "https://core.parts/php/everything.php.c.js" => 'console.warn("produce everything.php right now. it will need the conversion from json to php array...", "$Δ = [ $1 ]; eval(\"?>\" . $Δ[\"https://core.parts/php/index.php\"] . \"<?php \");");',
 "https://core.parts/?layout" => 'https://core.parts/layout.css',
 "https://core.parts/?manifest" => 'https://core.parts/os-95/manifest.uri',
 "https://core.parts/?onpointermove" => 'https://core.parts/onpointermove.js',
 "https://core.parts/?onpointerup" => 'https://core.parts/onpointerup.js',
 "https://core.parts/?oncontextmenu" => 'https://core.parts/oncontextmenu.js',
 "https://core.parts/const/zero.txt" => '0',
 "https://core.parts/const/one.txt" => '1',
 "https://core.parts/img/white-dots.png" => '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',
 "https://core.parts/img/white-grid.png" => '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',
 "https://core.parts/img/blue-grid.png" => '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',
 "https://core.parts/apple-touch-icon.png" => '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',
 "https://core.parts/behaviors/grab/fx.uri" => 'https://core.parts/onpointermove.js https://core.parts/onpointerup.js',
 "https://core.parts/behaviors/grab/src.uri" => '',
 "https://core.parts/behaviors/grab/src.uri?fx" => 'https://core.parts/behaviors/grab/fx.uri',
 "https://core.parts/behaviors/release/fx.uri" => 'https://core.parts/onpointerup.js',
 "https://core.parts/behaviors/release/src.uri" => '',
 "https://core.parts/behaviors/release/src.uri?fx" => 'https://core.parts/behaviors/release/fx.uri',
 "https://core.parts/behaviors/resize/onpointerdown.js?stop_propagation" => 'https://core.parts/const/zero.txt',
 "https://core.parts/behaviors/resize/onpointerdown.js?should_focus" => 'https://core.parts/const/zero.txt',
 "https://core.parts/behaviors/resize/onpointerdown.js?constructor" => 'https://core.parts/behaviors/resize/onpointerdown.c.js',
 "https://core.parts/behaviors/resize/bottom-right.css" => '
  :host {
   position: absolute;
   bottom: -2px;
   right: -2px;
   width: 6px;
   height: 6px;
   cursor: nwse-resize
  }',
 "https://core.parts/behaviors/resize/bottom-.css" => '
   :host {
    position: absolute;
    bottom: -2px;
    left: 4px;
    right: 4px;
    height: 6px;
    cursor: ns-resize
   }',
 "https://core.parts/behaviors/resize/left-.css" => '
    :host {
     position: absolute;
     bottom: 4px;
     left: -2px;
     top: 4px;
     width: 6px;
     cursor: ew-resize
    }',
 "https://core.parts/behaviors/resize/right-.css" => '
    :host {
     position: absolute;
     bottom: 4px;
     right: -2px;
     top: 4px;
     width: 6px;
     cursor: ew-resize
    }',
 "https://core.parts/behaviors/resize/top-left.css" => '
    :host {
     position: absolute;
     top: -2px;
     left: -2px;
     width: 6px;
     height: 6px;
     cursor: nwse-resize
    }',
 "https://core.parts/behaviors/resize/top-.css" => '
    :host {
     position: absolute;
     top: -2px;
     left: 4px;
     right: 4px;
     height: 6px;
     cursor: ns-resize
    }',
 "https://core.parts/behaviors/resize/bottom-left.css" => '
  :host {
   position: absolute;
   bottom: -2px;
   left: -2px;
   width: 6px;
   height: 6px;
   cursor: nesw-resize
  }',
 "https://core.parts/behaviors/resize/top-right.css" => '
  :host {
   position: absolute;
   top: -2px;
   right: -2px;
   width: 6px;
   height: 6px;
   cursor: nesw-resize
  }',
 "https://core.parts/behaviors/resize/onpointerdown.c.js" => '
  const
   input_url = position.headerOf().href,
   transformer_url = "https://core.parts/behaviors/resize/transformer.js",
   output_properties = {
    "move":      "[north_south_pan, east_west_pan]",
    "n-resize":  "north_resize",
    "s-resize":  "south_resize",
    "e-resize":  "east_resize",
    "w-resize":  "west_resize",
    "ne-resize": "[north_resize, east_resize]",
    "se-resize": "[south_resize, east_resize]",
    "nw-resize": "[north_resize, west_resize]",
    "sw-resize": "[south_resize, west_resize]"
   }[mode],
   stop = ("" + stop_propagation) === "1" ? `event.stopPropagation(); event.preventDefault()` : ``,
   focus = ("" + should_focus) === "1" ? `event.target.focus();` : ``;
  if (!output_properties) throw "bad mode: " + mode
  return `event => {
   const
    { clientX: x, clientY: y } = event,
    { x: X = 0, y: Y = 0, w: W = 0, h: H = 0, range = { }, snap = { } } = JSON.parse(Ω["${input_url}"].toPrimitive()),
    { x: rx = [], y: ry = [], w: rw = [], h: rh = [] } = range,
    { x: sx = 1, y: sy = 1 } = snap,
    [min_x = -Infinity, max_x = Infinity] = rx,
    [min_y = -Infinity, max_y = Infinity] = ry,
    [min_w = -Infinity, max_w = Infinity] = rw,
    [min_h = -Infinity, max_h = Infinity] = rh,
    original_properties = ${"`x: ${X}, y: ${Y}, w: ${W}, h: ${H}, range: { x: [${rx}], y: [${ry}], w: [${rw}], h: [${rh}] }, snap: { x: ${sx} , y: ${sy} }`"},
    north_south_pan = ${"`y: Math.max(${min_y}, ${Y} - ${y} + y)`"},
    east_west_pan = ${"`x: Math.max(${min_x}, ${X} - ${x} + x)`"},
    north_resize = [north_south_pan,${"`h: Math.max(${min_h}, ${H} + (${y} - y))`"}].join(", "),
    south_resize = ${"`h: Math.max(${min_h}, ${H} - (${y} - y))`"},
    east_resize = ${"`w: Math.max(${min_w}, ${W} - (${x} - x))`"},
    west_resize = [east_west_pan, ${"`w: Math.max(${min_w}, ${W} + (${x} - x))`"}].join(", "),
    properties = [original_properties, ${output_properties}].join(", ");
   
   ${stop}
   ${focus}
   Ω["${transformer_url}"] = \`({ clientX: x, clientY: y }) => {
    const object = {\${properties}};
    if (!object.x) delete object.x
    if (!object.y) delete object.y
    if (!object.h) delete object.h
    if (!object.w) delete object.w
    if (!object.snap.x) delete object.snap.x
    else {
     if ("x" in object) object.x = Math.round(object.x / object.snap.x) * object.snap.x
     if ("w" in object) object.w = Math.round(object.w / object.snap.x) * object.snap.x
    }
    if (!object.snap.y) delete object.snap.y
    else {
     if ("y" in object) object.y = Math.round(object.y / object.snap.y) * object.snap.y
     if ("h" in object) object.h = Math.round(object.h / object.snap.y) * object.snap.y
    }
    if (!Object.keys(object.snap).length) delete object.snap
    if (!object.range.x.length) delete object.range.x
    if (!object.range.y.length) delete object.range.y
    if (!object.range.w.length) delete object.range.w
    if (!object.range.h.length) delete object.range.h
    if (!Object.keys(object.range).length) delete object.range
    Ω["${input_url}"] = JSON.stringify(object)
   }\`
   Ω["https://core.parts/behaviors/grab/src.uri"] = "${transformer_url}";
  }`',
 "https://core.parts/behaviors/grab/position.json" => '{}',
 "https://core.parts/behaviors/move.txt" => "move",
 "https://core.parts/behaviors/resize/bottom-left.txt" => "sw-resize",
 "https://core.parts/behaviors/resize/bottom-right.txt" => "se-resize",
 "https://core.parts/behaviors/resize/bottom-.txt" => "s-resize",
 "https://core.parts/behaviors/resize/left-.txt" => "w-resize",
 "https://core.parts/behaviors/resize/right-.txt" => "e-resize",
 "https://core.parts/behaviors/resize/top-left.txt" => "nw-resize",
 "https://core.parts/behaviors/resize/top-right.txt" => "ne-resize",
 "https://core.parts/behaviors/resize/top-.txt" => "n-resize",
 "https://core.parts/behaviors/resize/top-bottom.txt" => "ns-resize",
 "https://core.parts/behaviors/resize/left-right.txt" => "ew-resize",
 "https://core.parts/behaviors/resize/top-left-bottom-right.txt" => "nwse-resize",
 "https://core.parts/behaviors/resize/top-right-bottom-left.txt" => "nesw-resize",
 "https://core.parts/behaviors/window-focus.c.js" => '
  const
   active_url = active.headerOf().href,
   window_url = window.headerOf().href;
  return `
   e => {
    Ω["${active_url}"] = "1";
    const
     windows_uri = "https://core.parts/os-95/windows.uri",
     windows_string = Ω[windows_uri].toPrimitive(),
     windows = windows_string ? windows_string.split(" ") : [],
     own_window = "${window_url}";
    if (windows.at(-1) !== own_window) {
     const window_index = windows.indexOf(own_window);
     if (window_index !== -1) windows.splice(window_index, 1)
     windows.push(own_window)
     Ω[windows_uri] = windows.join(" ")
    }
   }
  `',
 "https://core.parts/client-to-server.js" => '
  ({ data }) => {
   if (data === "restart") registration.unregister()
   else { Object.assign(Δ, data) }
  }',
 "https://core.parts/core-part/" => '
  <!DOCTYPE html>
  <script src="https://core.parts/everything.js"></script>
   <script>Ω["https://core.parts/core-part/bootstrap.js"]()</script>
  <meta name="viewport" content="width=device-width, initial-scale=0.5" />
  <style>
   html, body {
    overscroll-behavior-y: contain !important;
    overflow: clip;
    text-overflow: ellipsis;
    white-space: nowrap;
    height: 100%;
   }
  </style>',
 "https://core.parts/core-part/layout.css" => '',
 "https://core.parts/core-part/manifest.uri" => '',
 "https://core.parts/core-part/?apply" => 'https://core.parts/proxy/beta/apply.js',
 "https://core.parts/core-part/?get" => 'https://core.parts/proxy/beta/get.js',
 "https://core.parts/core-part/?getOwnPropertyDescriptor" => 'https://core.parts/proxy/beta/getOwnPropertyDescriptor.js',
 "https://core.parts/core-part/?getPrototypeOf" => 'https://core.parts/proxy/beta/getPrototypeOf.js',
 "https://core.parts/core-part/?has" => 'https://core.parts/proxy/beta/has.js',
 "https://core.parts/core-part/?headerOf" => 'https://core.parts/proxy/beta/headerOf.js',
 "https://core.parts/core-part/?isExtensible" => 'https://core.parts/proxy/beta/isExtensible.js',
 "https://core.parts/core-part/?layout" => 'https://core.parts/core-part/layout.css',
 "https://core.parts/core-part/?manifest" => 'https://core.parts/core-part/manifest.uri',
 "https://core.parts/core-part/?ownKeys" => 'https://core.parts/proxy/beta/ownKeys.js',
 "https://core.parts/core-part/?query" => 'https://core.parts/proxy/beta/query.js',
 "https://core.parts/core-part/?rootsOf" => 'https://core.parts/proxy/beta/rootsOf.js',
 "https://core.parts/core-part/?set" => 'https://core.parts/proxy/beta/set.js',
 "https://core.parts/core-part/?toPrimitive" => 'https://core.parts/proxy/beta/toPrimitive.js',
 "https://core.parts/core-part/?toString" => 'https://core.parts/proxy/beta/toString.js',
 "https://core.parts/core-part/?valueOf" => 'https://core.parts/proxy/beta/valueOf.js',
 "https://core.parts/core-part/?core" => 'https://core.parts/core-part/',
 "https://core.parts/core-part/?hash" => 'https://core.parts/core-part/hash.js',
 "https://core.parts/core-part/hash.js" => '(x, y = 0x811c9dc5) => [...x].reduce((y, c) => (y ^= c.charCodeAt(0)) + (y << 1) + (y << 4) + (y << 7) + (y << 8) + (y << 24), y).toString(36)',
 "https://core.parts/demo/hello.txt?mood" => 'https://core.parts/demo/mood.txt',
 "https://core.parts/demo/hello.txt?noun" => 'https://core.parts/demo/noun.txt',
 "https://core.parts/demo/hello.txt?past_phrasal_verb" => 'https://core.parts/demo/past_phrasal_verb.txt',
 "https://core.parts/demo/hello.txt?persons_name" => 'https://core.parts/demo/persons_name.txt',
 "https://core.parts/demo/hello.txt?time_interval" => 'https://core.parts/demo/time_interval.txt',
 "https://core.parts/demo/hello.txt?verb_ending_in_ing" => 'https://core.parts/demo/verb_ending_in_ing.txt',
 "https://core.parts/demo/hello.txt?constructor" => 'https://core.parts/demo/hello.txt.c.js',
 "https://core.parts/demo/hello.txt.c.js" => 'return `Welcome to my ${noun}, ${persons_name}! I\'ve been ${verb_ending_in_ing} on it all ${time_interval}. I\'m so ${mood} you ${past_phrasal_verb}.`',
 "https://core.parts/demo/mood.txt" => 'glad',
 "https://core.parts/demo/noun.txt" => 'website',
 "https://core.parts/demo/past_phrasal_verb.txt" => 'stopped by',
 "https://core.parts/demo/persons_name.txt" => 'stranger',
 "https://core.parts/demo/time_interval.txt" => 'day',
 "https://core.parts/demo/verb_ending_in_ing.txt" => 'working',
 "https://core.parts/core-part/bootstrap.js" => '
  () => {
   globalThis.nodePool = { }
   const eventKireji = { onclick: 0, oncontextmenu: 0, onpointerdown: 0, onpointerup: 0, onpointermove: 0, ondblclick: 0, onfocus: 0, layout: 1, manifest: 1, ondragstart: -1 };
   Object.defineProperties(HTMLElement.prototype, {
    shadow: {
     get() { if (!this._shadow) this._shadow = this.attachShadow({ mode: "closed" }); return this._shadow }
    },
    layout: {
     get() { if (!this._layout) { this._layout = new CSSStyleSheet(); this.shadow.adoptedStyleSheets.push(this._layout) } return this._layout },
     set(v) { this.layout.replaceSync(v) }
    },
    manifest: {
     get() { return [...this.shadow.children].map(x => x.url).join(" ") },
     set(v) {
      if (v === undefined) throw new TypeError(`manifest called on undefined (${this._url})`)
      if (typeof v !== "string") throw new TypeError(`part manifest must have mime of text/uri-list. Function expected js input "string", but got "${typeof v}." (${this._url})`)
      const C = this.shadow, O = [...C.children].map(x => x.url);
      if (v === "") {
       [...C.children].forEach(x => x.remove())
       return;
      }
      // deprecated
      // const repair = this._repair;
      // if (O.join(" ") === && !repair) return;
      if (O.join(" ") === v) return;
      const N = v.split(" ")
      let o, n, i = -1;
      while (O.length && N.length) {
       i++
       if ((o = O.shift()) !== (n = N.shift())) {
        const u = O.findIndex(x => x === n)
        if (u === -1) this.install(n, i)
        else { C.insertBefore(C.children[i + u + 1], C.children[i]); O.splice(u, 1) }
        if (N.some(x => x === o)) O.unshift(o)
        else C.children[i + 1].remove();
       }
       // if (repair) C.children[i].repair()
      }
      if (O.length) O.forEach(() => C.children[i + 1].remove())
      else if (N.length) N.forEach(x => this.install(x));
     }
    },
    install: {
     get() {
      return (url, index) => {
       if (!url || url === "undefined") throw new TypeError(`install url cannot be ${url === undefined? "undefined" : url === "" ? "an empty string" : `"${url}"`} (installing <${this.tagName}> on ${this._url})`)
       const
        poolNode = (url in nodePool ? [...nodePool[url]].find(x => !x.isConnected && !x.parentNode) : undefined),
        hadPoolNode = !!poolNode,
        node = hadPoolNode ? poolNode : document.createElement(Ω[url].headerOf().groups.part);
       if (index === undefined || index >= this.shadow.children.length) this.shadow.appendChild(node); else this.shadow.insertBefore(node, this.shadow.children[index])
       if (node._url !== url) node.url = url// deprecated ; else node.repair();
      }
     }
    },
    repair: {
     get() {
      return () => {
       console.warn("deprecated function called: repair")
       this._repair = true
       this.manifest = this.proxy.manifest?.toPrimitive() ?? ""
       this._repair = false
      }
     }
    },
    proxy: {
     get() { if (!this._proxy) this._proxy = Ω[this.url]; return this._proxy }
    },
    url: {
     get() { if (!this._url) throw new ReferenceError("attempted to get url before it was defined."); return this._url },
     set(v) {
      if (this._url) throw new TypeError(`cannot change HTMLElement\'s url (is ${this._url}, tried to set to ${v})`);
      // console.groupCollapsed("node > set url", { v })
      // console.trace()
      this._url = v
      if (!(v in nodePool)) nodePool[v] = new Set()
      nodePool[v].add(this)
      const proxy_keys = Object.keys(this.proxy), focus_events = ["onfocus", "onpointerdown", "onclick", "oncontextmenu"];
      if (proxy_keys.some(x => focus_events.includes(x))) this.tabIndex = 0
      for (const kireji in eventKireji) {
       const type = eventKireji[kireji];
       if (type === -1) {
        this[kireji] = e => e.preventDefault()
       } else if (proxy_keys.includes(kireji)) {
       const subproxy = this.proxy[kireji]
       switch(type) {
        case 0:
         this[kireji] = subproxy
         break;
        case 1:
         const primitive = subproxy.toPrimitive()
         // console.log(v, primitive)
         this[kireji] = primitive
         const url = subproxy.headerOf().href;
         if (!(url in causality)) causality[url] = {}
         if (!(kireji in causality[url])) causality[url][kireji] = new Set()
         causality[url][kireji].add(this)
         break;
        }
       }
      }
      // console.groupEnd()
     }
    }
   })
   onload = () => document.body.url = location.href;
  }',
 "https://core.parts/os-95/taskbar-/tray-/factory-reset/layout.css" => '
  :host {
   width: 16px;
   height: 16px;
   cursor: pointer;
  }
  :host::before {
   content: "🧼";
   font-size: 16px;
   line-height: 16px;
  }',
 "https://core.parts/os-95/taskbar-/tray-/factory-reset/?layout" => 'https://core.parts/os-95/taskbar-/tray-/factory-reset/layout.css',
 "https://core.parts/os-95/taskbar-/tray-/factory-reset/?onclick" => 'https://core.parts/os-95/taskbar-/start-menu/restart-server/onclick.js',
 "https://core.parts/file.js" => '
  event => {
   const
    direct = typeof event === "string",
    url = direct ? event : event.request.url;
   // console.groupCollapsed("file.js", { url });
   if (url === "https://core.parts/everything.js") {
    console.groupEnd()
    return event.respondWith(new Response("var causality = {}, onfetch = (Ω = new Proxy({}, new Proxy(" + JSON.stringify(Δ) + \', { get: (Δ, Υ) => eval(Δ[V = "https://core.parts/proxy/alpha.js"]) })))["https://core.parts/file.js"]\', { headers: { "content-type": "application/json" } }))
   }
   if (url.includes("&")) {
    throw "deprecated"
    if (!url.includes("?")) throw new TypeError(`bad format (ampersand with no query string) ${url}`)
    const [base, query] = url.split("?")
    query.split("&").forEach(subquery => {
     const
      url = base + "?" + subquery,
      proxy = Ω[url],
      { value, kireji, target } = proxy.headerOf().groups;
     Ω[target][kireji] = value
    })
    const response = new Response(new Int8Array([1]))
    return direct ? response : event.respondWith(response)
   }
   const proxy = Ω[url], { binary, type, value, kireji, target } = proxy.headerOf().groups;
   let string = "";
   if (value) {
    throw "deprecated"
    Ω[target][kireji] = value
    const response = new Response(new Int8Array([1]))
    return direct ? response : event.respondWith(response)
   }
   else {
    string = proxy.toPrimitive()
    if (kireji) {
     console.warn("deprecate this redirect concept?")
     const response = Response.redirect(string, 307);
     return direct ? response : event.respondWith(response)
    }
   }
   var body = new TextEncoder().encode(string);
   if (binary) {
    const B = atob(string), k = B.length, A = new ArrayBuffer(k), I = new Uint8Array(A);
    for (var i = 0; i < k; i++) I[i] = B.charCodeAt(i);
    body = new Blob([I], { type });
   }
   const response = new Response(body, { headers: { "content-type": `${type}${binary ? "" : "; charset=UTF-8"}`, "expires": "Sun, 20 Jul 1969 20:17:00 UTC", "server": "kireji" } })
   // console.groupEnd()
   return direct ? response : event.respondWith(response);
  }',
 "https://core.parts/flex-spacer/layout.css" => '
  :host {
   flex: 1 1;
  }',
 "https://core.parts/flex-spacer/?layout" => 'https://core.parts/flex-spacer/layout.css',
 "https://core.parts/os-95/taskbar-/tray-/fullscreen-/layout.css" => '
  :host {
   width: 16px;
   height: 16px;
   cursor: pointer;
  }
  :host::before {
   content: "⛶";
   font-size: 16px;
   line-height: 16px;
  }',
 "https://core.parts/os-95/taskbar-/tray-/fullscreen-/onclick.js" => '()=>document.documentElement.requestFullscreen()',
 "https://core.parts/os-95/taskbar-/tray-/fullscreen-/?layout" => 'https://core.parts/os-95/taskbar-/tray-/fullscreen-/layout.css',
 "https://core.parts/os-95/taskbar-/tray-/fullscreen-/?onclick" => 'https://core.parts/os-95/taskbar-/tray-/fullscreen-/onclick.js',
 "https://core.parts/layout.css?height" => 'https://core.parts/os-95/taskbar-/css-height.txt',
 "https://core.parts/layout.css?constructor" => 'https://core.parts/layout.css.c.js',
 "https://core.parts/layout.css.c.js" => '
  return `
   :host {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    box-sizing: border-box;
    height: 100%;
    margin: 0;
    display: grid;
    grid-template-rows: 1fr ${height};
    font: 11px / 16px sans-serif;
   }
  `',
 "https://core.parts/onpointermove.js" => '
  () => {
  }',
 "https://core.parts/onpointermove.js?behavior" => 'https://core.parts/behaviors/grab/src.uri',
 "https://core.parts/onpointermove.js?constructor" => 'https://core.parts/onpointermove.c.js',
 "https://core.parts/onpointermove.c.js" => 'return (""+behavior) ? (""+Ω[behavior]) : "( ) => { }"',
 "https://core.parts/oncontextmenu.js" => 'e => { e.preventDefault(); e.stopPropagation(); }',
 "https://core.parts/onpointerup.js?grab" => 'https://core.parts/behaviors/grab/src.uri',
 "https://core.parts/onpointerup.js?release" => 'https://core.parts/behaviors/release/src.uri',
 "https://core.parts/onpointerup.js?constructor" => 'https://core.parts/onpointerup.c.js',
 "https://core.parts/onpointerup.c.js" => 'return `e => { ${(""+grab) ? `Ω["https://core.parts/behaviors/grab/src.uri"] = ""; ` : ""}${(""+release) ? `Ω["${release}"](e); Ω["${release.headerOf().href}"] = ""; ` : ""}}`',
 "https://core.parts/os-95/desktop-/layout.css" => '
  :host {
   position: relative;
   width: 100%;
   box-sizing: border-box;
   height: 100%;
   margin: 0;
   background: #377f7f;
  }',
 "https://core.parts/os-95/desktop-/onfocus.js?selected" => 'https://core.parts/os-95/taskbar-/selected.txt',
 "https://core.parts/os-95/desktop-/onfocus.js?constructor" => 'https://core.parts/os-95/desktop-/onfocus.c.js',
 "https://core.parts/os-95/desktop-/onfocus.c.js" => '
  const has_active = "" + selected !== "-1", active_url = selected.headerOf().href;
  return `
   () => {
    ${has_active ? `Ω["${active_url}"] = "-1"` : ``}
   }
  `',
 "https://core.parts/os-95/desktop-/?layout" => 'https://core.parts/os-95/desktop-/layout.css',
 "https://core.parts/os-95/desktop-/?onfocus" => 'https://core.parts/os-95/desktop-/onfocus.js',
 "https://core.parts/os-95/horizontal-line/layout.css" => '
  :host {
   height: 2px;
   border-top: 1px solid #7f7f7f;
   border-bottom: 1px solid white;
   box-sizing: border-box;
   margin: 4px 0;
  }',
 "https://core.parts/os-95/horizontal-line/?layout" => 'https://core.parts/os-95/horizontal-line/layout.css',
 "https://core.parts/os-95/manifest.uri?windows" => 'https://core.parts/os-95/windows.uri',
 "https://core.parts/os-95/manifest.uri?start_menu" => 'https://core.parts/os-95/start-menu/open.txt',
 "https://core.parts/os-95/manifest.uri?constructor" => 'https://core.parts/os-95/manifest.uri.c.js',
 "https://core.parts/os-95/manifest.uri.c.js" => '
  const
   urls = ["https://core.parts/os-95/desktop-/"],
   list = ("" + windows).split(" ").forEach(url => {
    if (("" + Ω[url + "minimized.txt"]) === "0") urls.push(url)
   })
  urls.push("https://core.parts/os-95/taskbar-/");
  if (""+start_menu === "1") urls.push(
   "https://core.parts/os-95/taskbar-/start-menu/click-to-close/",
   "https://core.parts/os-95/taskbar-/start-menu/"
  );
  return urls.join(" ")',
 "https://core.parts/os-95/programs/locate-/task-/datum.txt" => 'https://core.parts/os-95/programs/locate-/window-/active.txt',
 "https://core.parts/os-95/programs/locate-/task-/index.txt" => '1',
 "https://core.parts/os-95/programs/locate-/task-/index.txt?datum" => 'https://core.parts/os-95/programs/locate-/task-/datum.txt',
 "https://core.parts/os-95/programs/locate-/task-/index.txt?fx" => 'https://core.parts/os-95/programs/locate-/task-/index/fx.uri',
 "https://core.parts/os-95/programs/locate-/task-/index.txt?order" => 'https://core.parts/os-95/taskbar-/selected/fx.uri',
 "https://core.parts/os-95/programs/locate-/task-/index.txt?constructor" => 'https://core.parts/os-95/programs/locate-/task-/index.txt.c.js',
 "https://core.parts/os-95/programs/locate-/task-/index.txt.c.js" => 'return ""+(""+order).split(" ").indexOf(""+datum)',
 "https://core.parts/os-95/programs/locate-/task-/index/fx.uri" => 'https://core.parts/os-95/programs/locate-/window-/active.txt',
 "https://core.parts/os-95/programs/locate-/task-/layout.css?open" => 'https://core.parts/os-95/programs/locate-/window-/active.txt',
 "https://core.parts/os-95/programs/locate-/task-/layout.css?constructor" => 'https://core.parts/os-95/programs/locate-/task-/layout.css.c.js',
 "https://core.parts/os-95/programs/locate-/task-/layout.css.c.js" => 'return `
  :host {
   position: relative;
   height: 100%;
   margin: 0;
   width: 160px;
   display: flex;
   flex-flow: row nowrap;
   gap: 3px;
   border: none;${("" + open) === "1" ? `
   font: bold 11px sans-serif;` : ""}
   box-sizing: border-box;
   padding: ${("" + open) === "0" ? 3 : 4}px 2px 2px;
   text-align: left;
   box-shadow: ${("" + open) === "0" ? "inset -1px -1px black, inset 1px 1px white, inset -2px -2px #7a7a7a, inset 2px 2px #dbdbdb" : "inset -1px -1px white, inset 1px 1px black, inset -2px -2px #dbdbdb, inset 2px 2px #7a7a7a"}
  }
  :host(:focus)::after {
   border: 1px dotted black;
   content: "";
   position: absolute;
   margin: 3px;
   left: 0;
   right: 0;
   top: 0;
   bottom: 0;
   pointer-events: none;
  }${("" + open) === "1" ? `
  :host > * {
   z-index: 3
  }
  :host::before {
   content: "";
   position: absolute;
   margin: 2px;
   border-top: 1px solid white;
   left: 0;
   right: 0;
   top: 0;
   bottom: 0;
   background-image:
    linear-gradient(45deg, white 25%, transparent 25%, transparent 75%, white 75%, white),
    linear-gradient(45deg, white 25%, transparent 25%, transparent 75%, white 75%, white);
   background-size: 2px 2px;background-position: 0 0, 1px 1px;
  }` : ``}
  app-icon {
   width: 16px;
   height: 16px;
  }`',
 "https://core.parts/os-95/programs/locate-/task-/manifest.uri" => 'https://core.parts/os-95/icons/folder-icon/ https://core.parts/os-95/programs/locate-/app-label/',
 "https://core.parts/os-95/programs/locate-/task-/manifest.uri?open" => 'https://core.parts/os-95/programs/locate-/window-/active.txt',
 "https://core.parts/os-95/programs/locate-/task-/onpointerdown.js?minimized" => 'https://core.parts/os-95/programs/locate-/window-/minimized.txt',
 "https://core.parts/os-95/programs/locate-/task-/onpointerdown.js?active" => 'https://core.parts/os-95/programs/locate-/window-/active.txt',
 "https://core.parts/os-95/programs/locate-/task-/onpointerdown.js?window" => 'https://core.parts/os-95/programs/locate-/window-/',
 "https://core.parts/os-95/programs/locate-/task-/onpointerdown.js?core" => 'https://core.parts/os-95/programs/relate-/task-/onpointerdown.js',
 "https://core.parts/os-95/programs/locate-/task-/open/fx.uri" => 'https://core.parts/os-95/programs/locate-/task-/layout.css https://core.parts/os-95/taskbar-/selected.txt https://core.parts/os-95/programs/locate-/window-/layout.css https://core.parts/os-95/programs/locate-/task-/onpointerdown.js',
 "https://core.parts/os-95/programs/locate-/task-/?layout" => 'https://core.parts/os-95/programs/locate-/task-/layout.css',
 "https://core.parts/os-95/programs/locate-/task-/?manifest" => 'https://core.parts/os-95/programs/locate-/task-/manifest.uri',
 "https://core.parts/os-95/programs/locate-/task-/?onpointerdown" => 'https://core.parts/os-95/programs/locate-/task-/onpointerdown.js',
 "https://core.parts/os-95/programs/locate-/app-label/layout.css?address" => 'https://core.parts/os-95/programs/locate-/window-/address.uri',
 "https://core.parts/os-95/programs/locate-/app-label/layout.css?constructor" => 'https://core.parts/os-95/programs/locate-/app-label/layout.css.c.js',
 "https://core.parts/os-95/programs/locate-/app-label/layout.css.c.js" => 'return `
  :host {
   margin: 0;
   height: 16px;
   vertical-align: center;
   text-overflow: ellipsis;
   overflow: clip;
  }
  :host::after {
   content: "Locate - ${address}";
   white-space: nowrap;
  }`',
 "https://core.parts/os-95/programs/locate-/app-label/?layout" => 'https://core.parts/os-95/programs/locate-/app-label/layout.css',
 "https://core.parts/os-95/programs/locate-/start-menu-item/app-label/layout.css" => ':host::after {
    height: 24px;
    content: "Locate";
   }',
 "https://core.parts/os-95/programs/locate-/start-menu-item/app-label/?layout" => 'https://core.parts/os-95/programs/locate-/start-menu-item/app-label/layout.css',
 "https://core.parts/os-95/programs/locate-/start-menu-item/layout.css" => '
   :host {
    position: relative;
    display: flex;
    flex-flow: row nowrap;
    align-items: center;
    padding: 4px 0 }
   :host(:hover) {
    background: #00007f;
    color: white }
   folder-icon {
    width: 24px;
    height: 24px;
    margin: 0 10px;
    --size: 24px;
   }',
 "https://core.parts/os-95/programs/locate-/start-menu-item/manifest.uri" => 'https://core.parts/os-95/icons/folder-icon/ https://core.parts/os-95/programs/locate-/start-menu-item/app-label/',
 "https://core.parts/os-95/programs/locate-/start-menu-item/?layout" => 'https://core.parts/os-95/programs/locate-/start-menu-item/layout.css',
 "https://core.parts/os-95/programs/locate-/start-menu-item/?manifest" => 'https://core.parts/os-95/programs/locate-/start-menu-item/manifest.uri',
 "https://core.parts/os-95/programs/locate-/start-menu-item/?onclick" => 'https://core.parts/os-95/programs/locate-/task-/onpointerdown.js',
 "https://core.parts/os-95/programs/locate-/window-/active.txt" => '0',
 "https://core.parts/os-95/programs/locate-/window-/active.txt?fx" => 'https://core.parts/os-95/programs/locate-/task-/open/fx.uri',
 "https://core.parts/os-95/programs/locate-/window-/active.txt?index" => 'https://core.parts/os-95/programs/locate-/task-/index.txt',
 "https://core.parts/os-95/programs/locate-/window-/active.txt?minimized" => 'https://core.parts/os-95/programs/locate-/window-/minimized.txt',
 "https://core.parts/os-95/programs/locate-/window-/active.txt?selected" => 'https://core.parts/os-95/taskbar-/selected.txt',
 "https://core.parts/os-95/programs/locate-/window-/active.txt?constructor" => 'https://core.parts/os-95/programs/locate-/window-/active.txt.c.js',
 "https://core.parts/os-95/programs/locate-/window-/active.txt.c.js" => 'return ("" + minimized) === "1" ? "0" : ("" + selected) === ("" + index) ? "1" : "0"',
 "https://core.parts/os-95/programs/locate-/window-/address-fx.uri" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/files-/manifest.uri https://core.parts/os-95/programs/locate-/app-label/layout.css',
 "https://core.parts/os-95/programs/locate-/window-/address.uri" => 'https://core.parts/os-95/programs/relate-/window-/graph-/',
 "https://core.parts/os-95/programs/locate-/window-/address.uri?fx" => 'https://core.parts/os-95/programs/locate-/window-/address-fx.uri',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/exit-button/layout.css" => '
  :host {
   position: relative;
   width: 16px;
   height: 14px;
   background: #c3c3c3;
   margin-left: 2px;
   box-shadow: inset -1px -1px black, inset 1px 1px white, inset -2px -2px #7a7a7a, inset 2px 2px #dbdbdb }
  :host::before, :host::after {
   --color: #7f7f7f;
   content: "";
   display: block;
   position: absolute;
   width: 8px;
   height: 7px;
   left: 4px;
   top: 3px;
   background: linear-gradient(to top left, transparent 0%, transparent calc(50% - 1px), var(--color) calc(50% - 1px), var(--color) calc(50% + 1px),  transparent calc(50% + 1px),  transparent 100%), linear-gradient(to top right,  transparent 0%,  transparent calc(50% - 1px), var(--color) calc(50% - 1px), var(--color) calc(50% + 1px),  transparent calc(50% + 1px),  transparent 100%) }
  :host::before {
   --color: white;
   left: 5px;
   top: 4px;
  }',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/exit-button/?layout" => 'https://core.parts/os-95/programs/locate-/window-/window-controls/exit-button/layout.css',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/column-fx.uri" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/layout.css https://core.parts/os-95/programs/locate-/window-/explorer-view/files-/layout.css',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/files-/layout.css?name_width" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/name-button/position.json',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/files-/layout.css?type_width" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/type-button/position.json',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/files-/layout.css?size_width" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/size-button/position.json',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/files-/layout.css?constructor" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/files-/layout.css.c.js',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/files-/layout.css.c.js" => 'return `
  :host {
   position: relative;
   display: grid;
   grid-template-columns: ${JSON.parse(""+name_width).w}px ${JSON.parse("" + type_width).w}px ${JSON.parse("" + size_width).w}px;
   grid-auto-rows: 18px;
   flex: 1 1;
   overflow: auto;
  }`',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/files-/manifest.uri?address" => 'https://core.parts/os-95/programs/locate-/window-/address.uri',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/files-/manifest.uri?cell" => 'https://core.parts/components/cell-/construct.js',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/files-/manifest.uri?click" => 'https://core.parts/components/click-/construct.js',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/files-/manifest.uri?fx" => 'https://core.parts/os-95/programs/locate-/window-/status/fx.uri',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/files-/manifest.uri?header_json" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/list.json',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/files-/manifest.uri?show_kireji" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/show_kireji.txt',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/files-/manifest.uri?label_css" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/label_css.js',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/label_css.js" => 'x => `:host { overflow: clip; text-overflow: ellipsis; line-height: 18px } :host::before { content: "${x}" }`',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/files-/manifest.uri?item_layout" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/item_layout.css',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/item_layout.css" => '
  :host {
   position: relative;
   display: flex;
   flex-flow: row nowrap;
   align-items: center;
   padding: 2px 0;
   overflow: clip;
   box-sizing: border-box;
   padding-right: 6px;
  }
  :host>:first-child {
   --size: 16px;
   margin-right: 4px
  }
  :host(:focus) {
   background: silver;
   width: min-content;
   background: #00007f;
   color: white;
   outline: 1px dotted black;
  }',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/files-/manifest.uri?sort_order" => 'https://core.parts/os-95/programs/locate-/window-/sort_order.json',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/files-/manifest.uri?constructor" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/files-/manifest.uri.c.js',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/files-/manifest.uri.c.js" => '
  const
   browse_url = "" + address, icon_urlbase = "https://core.parts/os-95/icons/", fileurlbase = υ.replace(/\/manifest.uri$/, "/file-"), file_list = [], url_list = [], O = JSON.parse(sort_order), K = Object.keys(O), header = JSON.parse(header_json),
   file_urls = Object.keys(Δ).filter(url => (url !== browse_url) && url.startsWith(browse_url)).map(x => x.replace(browse_url, "").includes("/") ? x.slice(0, browse_url.length + x.replace(browse_url, "").indexOf("/") + 1) : x);
  file_urls.push(...file_urls.filter(x => x.includes("?") && x.split("?")[0] !== browse_url).map(x =>x.split("?")[0]));
  const filenames = [...new Set(file_urls)].map(url => [url, url.replace(browse_url, "")])
  let kireji_count = 0, folder_count = 0, file_count = 0;
  for (const [url, name, i] of filenames) {
   if (name.includes("?") && ("" + show_kireji === "0")) continue;
   const
    proxy = Ω[url],
    groups = proxy.headerOf().groups,
    row_data = {
     ...groups,
     size: groups.size,
     entry_size: groups.entry_size,
     name,
     url,
     manifest: [],
     type: url.match(/\?[\w\d_$]+$/)
      ? (kireji_count++, "kireji") : url.endsWith("/")
      ? (folder_count++, url.match(/[^:]\/$/)
         ? url.match(/^https:\/\/[\w\d]+\.[\w\d]{2,}\/$/)
         ? "domain" : "folder" :
           "protocol" )
      : (file_count++, groups.type),
     size_label: groups.size + " byte" + (groups.size === 1 ? "" : "s")
    },
    is_index = ["folder", "domain", "protocol"].includes(row_data.type),
    item_url = fileurlbase + hash(url) + "-",
    label_url = item_url + "app-label/",
    icontag = row_data.type.replace(/[^a-zA-Z0-9]+/g, "-") + "-icon",
    icon_url = icon_urlbase + icontag + "/",
    item_manifest = icon_url + " " + label_url,
    focus_item_url = item_url + "onfocus.js",
    open_item_url = item_url + "open.js";
   
   Ω[focus_item_url] = `() => { [...nodePool["${item_url + "name-/"}"]].find(x => x.isConnected).focus() }`
   Ω[open_item_url] = `() => { ${is_index ? `Ω["https://core.parts/os-95/programs/locate-/window-/address.uri"] = "${url + (row_data.type === "protocol" ? "/": "")}"` : `Ω["https://core.parts/os-95/programs/relate-/window-/address.uri"] = "${url + (row_data.type === "protocol" ? "/": "")}"`} }`
   for (const key in header) {
    const keyurl = item_url + key + "-/";
    Ω[keyurl + "?onfocus"] = focus_item_url
    row_data.manifest.push(keyurl)
    if (key === "name") {
     cell(label_url, label_css(["folder", "domain"].includes(row_data.type) ? name.slice(0, -1) : name))
     cell(keyurl, ""+item_layout, item_manifest)
    } else {
     cell(keyurl, label_css(row_data[key + (key === "size" ? "_label" : "")]))
    }
    click(keyurl, undefined, open_item_url)
   }
   file_list.push(row_data)
  }
  Ω["https://core.parts/os-95/programs/locate-/window-/status/file_count.txt"] = file_count
  Ω["https://core.parts/os-95/programs/locate-/window-/status/folder_count.txt"] = folder_count
  Ω["https://core.parts/os-95/programs/locate-/window-/status/kireji_count.txt"] = kireji_count
  file_list.sort((a, b) => {
   const c = (((a[K[0]] > b[K[0]]) === O[K[0]]) ? 1 : (a[K[0]] === b[K[0]] ? (((a[K[1]] > b[K[1]]) === O[K[1]]) ? 1 : (a[K[1]] === b[K[1]] ? (((a[K[2]] > b[K[2]]) === O[K[2]]) ? 1 : (a[K[2]] === b[K[2]] ? 0 : -1)) : -1)) : -1))
   return c;
  })
  url_list.push(...file_list.map(({ manifest }) => manifest).flat())
  return url_list.join(" ")',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/files-/?layout" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/files-/layout.css',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/files-/?manifest" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/files-/manifest.uri',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/layout.css?name_width" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/name-button/position.json',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/layout.css?type_width" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/type-button/position.json',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/layout.css?size_width" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/size-button/position.json',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/layout.css?constructor" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/layout.css.c.js',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/layout.css.c.js" => 'return `:host {
   display: grid;
   width: 100%;
   grid-template-columns: ${JSON.parse(""+name_width).w}px ${JSON.parse("" + type_width).w}px ${JSON.parse("" + size_width).w}px 1fr;
  }`',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/manifest.uri?button" => 'https://core.parts/components/button-/construct.js',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/manifest.uri?fx" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/manifest-fx.uri',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/manifest-fx.uri?sort_order" => 'https://core.parts/os-95/programs/locate-/window-/sort_order.json',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/manifest-fx.uri?constructor" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/manifest-fx.uri.c.js',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/manifest-fx.uri.c.js" => 'return Object.keys(JSON.parse(""+sort_order)).map(key => `https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/${key}-button/layout.css`).join(" ")',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/manifest.uri?headers" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/list.json',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/manifest.uri?item_layout" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/item_layout.css',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/item_layout.css" => '
  :host {
   position: relative;
   width: 100%;
   text-overflow: ellipsis;
   overflow: clip;
   white-space: nowrap;
   line-height: 18px;
  }
  :host::before {
   vertical-align: center;
   margin-left: 6px;
   width: calc(100% - 22px);
  }',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/manifest.uri?sort_order" => 'https://core.parts/os-95/programs/locate-/window-/sort_order.json',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/manifest.uri?constructor" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/manifest.uri.c.js',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/manifest.uri.c.js" => '
  const header_obj = JSON.parse("" + headers), urls = []
  const string_order = "" + sort_order, my_order = JSON.parse(string_order), first_key = Object.keys(my_order)[0], first_dir = my_order[first_key];
  Object.keys(header_obj).forEach((key, i, arr) => {
   button(
    urls[i] = "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/" + key + "-button/",
    `${item_layout}
    :host::before {
     content: "${header_obj[key]}";
    }${first_key === key ? `
    :host::after {
     --size: 8px;
     position: absolute;
     right: 5px;
     top: 5px;
     width: var(--size);
     height: var(--size);
     content: "${ first_dir ? "▼" : "▲" }";
     font-size: var(--size);
     line-height: var(--size);
     text-align: center;
     vertical-align: center;
    }`: ``}`,
    `${urls[i]}resize-/`,
    `() => {
     let order = ${string_order};
     const
      keys = Object.keys(order),
      key = "${key}",
      keyplace = keys.indexOf(key);
     if (keyplace !== 0) {
      keys.splice(keyplace, 1);
      keys.unshift(key);
      order = keys.reduce((o, k) => (o[k]=order[k],o), {})
     }
     order[key] = (keyplace !== 0) || !order[key];
     Ω["${sort_order.headerOf().href}"] = JSON.stringify(order)
    }`
   )
  })
  urls.push("https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/filler-/")
  return urls.join(" ")',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/name-button/position.json" => '{ "w": 128, "range": { "w": [0] } }',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/name-button/position.json?fx" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/column-fx.uri',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/name-button/resize-/?core" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/resize-/',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/name-button/resize-/?onpointerdown" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/name-button/resize-/onpointerdown.js',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/name-button/resize-/onpointerdown.js?core" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/resize-/onpointerdown.js',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/name-button/resize-/onpointerdown.js?position" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/name-button/position.json',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/type-button/position.json" => '{ "w": 64, "range": { "w": [0] } }',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/type-button/position.json?fx" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/column-fx.uri',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/type-button/resize-/?core" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/resize-/',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/type-button/resize-/?onpointerdown" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/type-button/resize-/onpointerdown.js',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/type-button/resize-/onpointerdown.js?core" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/resize-/onpointerdown.js',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/type-button/resize-/onpointerdown.js?position" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/type-button/position.json',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/size-button/position.json" => '{ "w": 96, "range": { "w": [0] } }',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/size-button/position.json?fx" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/column-fx.uri',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/size-button/resize-/?core" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/resize-/',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/size-button/resize-/?onpointerdown" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/size-button/resize-/onpointerdown.js',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/size-button/resize-/onpointerdown.js?core" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/resize-/onpointerdown.js',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/size-button/resize-/onpointerdown.js?position" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/size-button/position.json',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/filler-/?layout" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/filler-/layout.css',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/filler-/layout.css" => ':host { background: #c3c3c3; box-shadow: inset -1px -1px black, inset 1px 1px white, inset -2px -2px #7a7a7a, inset 2px 2px #dbdbdb }',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/resize-/onpointerdown.js?mode" => 'https://core.parts/behaviors/resize/right-.txt',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/resize-/onpointerdown.js?core" => 'https://core.parts/behaviors/resize/onpointerdown.js',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/resize-/onpointerdown.js?stop_propagation" => 'https://core.parts/const/one.txt',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/resize-/onclick.js" => 'e => { e.stopPropagation() }',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/resize-/?onclick" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/resize-/onclick.js',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/resize-/?layout" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/resize-/layout.css',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/resize-/layout.css" => '
  :host {
   z-index: 1;
   position: absolute;
   right: -4px;
   width: 8px;
   cursor: col-resize;
   top: 0;
   bottom: 0;
  }',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/?layout" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/layout.css',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/?manifest" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/manifest.uri',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/list.json" => '{"name":"Name","type":"Type","size":"Size"}',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/show_kireji.txt" => '0',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/show_kireji.txt?fx" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/show_kireji.txt.fx.uri',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/show_kireji.txt.fx.uri" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/files-/manifest.uri https://core.parts/os-95/programs/locate-/window-/status-bar/layout.css https://core.parts/os-95/programs/locate-/window-/tool-bar/manifest.uri https://core.parts/os-95/programs/locate-/window-/tool-bar/toggle-kireji/layout.css',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/layout.css" => '
  :host {
   position: relative;
   flex: 1 1;
   box-shadow: -0.5px -0.5px 0 0.5px black, 0 0 0 1px #dbdbdb, -0.5px -0.5px 0 1.5px #7a7a7a, 0 0 0 2px white;
   background: white;
   margin: 2px;
   display: grid;
   grid-template-rows: 18px 1fr;
   overflow: clip;
   height: 100%;
  }',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/manifest.uri" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/ https://core.parts/os-95/programs/locate-/window-/explorer-view/files-/',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/?layout" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/layout.css',
 "https://core.parts/os-95/programs/locate-/window-/explorer-view/?manifest" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/manifest.uri',
 "https://core.parts/os-95/programs/locate-/window-/layout.css?active" => 'https://core.parts/os-95/programs/locate-/window-/active.txt',
 "https://core.parts/os-95/programs/locate-/window-/layout.css?maximized" => 'https://core.parts/os-95/programs/locate-/window-/maximized.txt',
 "https://core.parts/os-95/programs/locate-/window-/layout.css?position" => 'https://core.parts/os-95/programs/locate-/window-/position.json',
 "https://core.parts/os-95/programs/locate-/window-/layout.css?constructor" => 'https://core.parts/os-95/programs/locate-/window-/layout.css.c.js',
 "https://core.parts/os-95/programs/locate-/window-/layout.css.c.js" => '
  const common = `
     position: absolute;
     display: flex;
     flex-flow: column nowrap;
     gap: 2px; background: #c3c3c3;
     box-sizing: border-box;`,
   titlebar = ("" + active) === "1" ? `title-bar {
     background: rgb(0, 0, 163);
    }` : ``;
     
  if (("" + maximized) === "1") {
   return `
    :host {
     position: absolute;
     top: 0;
     left: 0;
     right: 0;
     bottom: 28px;
     padding: 2px;
     ${common};
    }
    ${titlebar}
   `
  } else {
   const { x = 0, y = 0, w = 0, h = 0 } = JSON.parse("" + position);
   return `
    :host {
     width: ${w}px;
     height: ${h}px;
     left: ${x}px;
     top: ${y}px;
     min-height: fit-content;
     padding: 4px;
     background: #c3c3c3;
     box-shadow:
      inset -1px -1px black,
      inset 1px 1px #c3c3c3,
      inset -2px -2px #7a7a7a,
      inset 2px 2px white,
      5px 7px 3px #0002;
     ${common};
    }
    ${titlebar}
   `
  }',
 "https://core.parts/os-95/programs/locate-/window-/manifest.uri?title" => 'https://core.parts/os-95/programs/locate-/window-/title-bar/',
 "https://core.parts/os-95/programs/locate-/window-/manifest.uri?tools" => 'https://core.parts/os-95/programs/locate-/window-/tool-bar/',
 "https://core.parts/os-95/programs/locate-/window-/manifest.uri?explorer" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/',
 "https://core.parts/os-95/programs/locate-/window-/manifest.uri?status" => 'https://core.parts/os-95/programs/locate-/window-/status-bar/',
 "https://core.parts/os-95/programs/locate-/window-/manifest.uri?transform_path" => 'https://core.parts/os-95/programs/locate-/window-/transform-/',
 "https://core.parts/os-95/programs/locate-/window-/manifest.uri?transform" => 'https://core.parts/components/transform-/construct.js',
 "https://core.parts/os-95/programs/locate-/window-/manifest.uri?position" => 'https://core.parts/os-95/programs/locate-/window-/position.json',
 "https://core.parts/os-95/programs/locate-/window-/manifest.uri?constructor" => 'https://core.parts/os-95/programs/locate-/window-/manifest.uri.c.js',
 "https://core.parts/os-95/programs/locate-/window-/manifest.uri.c.js" => <<<JS
  const [title_url, tools_url, explorer_url, status_url, transform_url, position_url] = [title, tools, explorer, status, transform_path, position].map(x => x.headerOf().href)
  const transform_urls = transform(transform_url, position_url, "nesw", title_url);
  return [title_url, tools_url, explorer_url, status_url, transform_urls].join(" ")
  JS,
 "https://core.parts/os-95/programs/locate-/window-/window-controls/maximize-button/down-fx.uri" => 'https://core.parts/os-95/programs/locate-/window-/window-controls/maximize-button/layout.css',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/maximize-button/down.txt" => '0',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/maximize-button/down.txt?fx" => 'https://core.parts/os-95/programs/locate-/window-/window-controls/maximize-button/down-fx.uri',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/maximize-button/layout.css?down" => 'https://core.parts/os-95/programs/locate-/window-/window-controls/maximize-button/down.txt',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/maximize-button/layout.css?constructor" => 'https://core.parts/os-95/programs/locate-/window-/window-controls/maximize-button/layout.css.c.js',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/maximize-button/layout.css.c.js" => 'return `:host {
   position: relative;
   width: 16px;
   height: 14px;
   background: #c3c3c3;
   box-shadow: ${(\'\'+down) === \'1\' ? \'inset -1px -1px white, inset 1px 1px black, inset -2px -2px #dbdbdb, inset 2px 2px #7a7a7a\' : \'inset -1px -1px black, inset 1px 1px white, inset -2px -2px #7a7a7a, inset 2px 2px #dbdbdb\'}
  }
  :host::before {
   --color: black;
   display: block;
   position: absolute;
   content: \'\';
   width: 9px;
   height: 9px;
   top: 2px;
   left: 3px;
   box-shadow: inset 0 2px var(--color), inset 0 0 0 1px var(--color)
  }
  :host(:hover)::before {
   --color: blue }`',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/maximize-button/manifest.uri" => '',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/maximize-button/onclick.js" => '
  () => {
   Ω[\'https://core.parts/os-95/programs/locate-/window-/maximized.txt\'] = \'1\'
  }',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/maximize-button/onpointerdown.js" => 'e => { e.stopPropagation(); Ω[\'https://core.parts/os-95/programs/locate-/window-/window-controls/maximize-button/down.txt\'] = \'1\'; Ω[\'https://core.parts/behaviors/release/src.uri\'] = \'https://core.parts/os-95/programs/locate-/window-/window-controls/maximize-button/release.js\'
  }',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/maximize-button/release.js" => 'e => { Ω[\'https://core.parts/os-95/programs/locate-/window-/window-controls/maximize-button/down.txt\'] = \'0\'
  }',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/maximize-button/?layout" => 'https://core.parts/os-95/programs/locate-/window-/window-controls/maximize-button/layout.css',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/maximize-button/?manifest" => 'https://core.parts/os-95/programs/locate-/window-/window-controls/maximize-button/manifest.uri',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/maximize-button/?onclick" => 'https://core.parts/os-95/programs/locate-/window-/window-controls/maximize-button/onclick.js',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/maximize-button/?onpointerdown" => 'https://core.parts/os-95/programs/locate-/window-/window-controls/maximize-button/onpointerdown.js',
 "https://core.parts/os-95/programs/locate-/window-/maximized.txt" => '0',
 "https://core.parts/os-95/programs/locate-/window-/maximized.txt?fx" => 'https://core.parts/os-95/programs/locate-/window-/maximized/fx.uri',
 "https://core.parts/os-95/programs/locate-/window-/maximized/fx.uri" => 'https://core.parts/os-95/programs/locate-/window-/layout.css https://core.parts/os-95/programs/locate-/window-/window-controls/manifest.uri https://core.parts/os-95/programs/locate-/window-/title-bar/ondblclick.js',
 "https://core.parts/os-95/programs/locate-/window-/tool-bar/layout.css" => '
  :host {
   height: 18px;
   display: flex;
   flex-flow: row nowrap;
   gap: 4px;
   align-items: center;
   padding: 2px;
  }
  :host > * {
   box-shadow:
  }',
 "https://core.parts/os-95/programs/locate-/window-/tool-bar/manifest.uri?button" => 'https://core.parts/components/button-/construct.js',
 "https://core.parts/os-95/programs/locate-/window-/tool-bar/manifest.uri?show_kireji" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/show_kireji.txt',
 "https://core.parts/os-95/programs/locate-/window-/tool-bar/manifest.uri?constructor" => 'https://core.parts/os-95/programs/locate-/window-/tool-bar/manifest.uri.c.js',
 "https://core.parts/os-95/programs/locate-/window-/tool-bar/manifest.uri.c.js" => '
  const
   common_css = ":host { cursor: pointer; --size: 16px; min-width: calc(var(--size) + 4px); padding: 2px; height: calc(var(--size) + 4px); font-size: var(--size); line-height: var(--size); display: flex; flex-flow: row nowrap } :host::before { content: \'\' } :host::after { padding: 0 2px; font-size: 11px }",
   common_url = "https://core.parts/os-95/programs/locate-/window-/tool-bar/";
  return [[
   common_url + "go-up/",
   common_css + ":host::before { content: \'📁\' } :host::after { content: \'Enclosing Folder\' }",
   "",
   `() => { const url = ("" + Ω["https://core.parts/os-95/programs/locate-/window-/address.uri"]).match(${/^.*?(?=[^/]*\/*$)/})[0]; Ω["https://core.parts/os-95/programs/locate-/window-/address.uri"] = url }`,
  ],[
   common_url + "toggle-kireji/",
   common_css + `:host::before { content: \'🔗\' } :host::after { content: \'${("" + show_kireji) === "0" ? "Show" : "Hide"} Kireji\' }`,
   "",
   `() => { Ω["https://core.parts/os-95/programs/locate-/window-/explorer-view/show_kireji.txt"] = (Ω["https://core.parts/os-95/programs/locate-/window-/explorer-view/show_kireji.txt"].toPrimitive() === "1") ? "0" : "1" }`,
  ]].map($ => { button(...$); return $[0] }).join(" ")',
 "https://core.parts/os-95/programs/locate-/window-/tool-bar/?layout" => 'https://core.parts/os-95/programs/locate-/window-/tool-bar/layout.css',
 "https://core.parts/os-95/programs/locate-/window-/tool-bar/?manifest" => 'https://core.parts/os-95/programs/locate-/window-/tool-bar/manifest.uri',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/minimize-button/down-fx.uri" => 'https://core.parts/os-95/programs/locate-/window-/window-controls/minimize-button/layout.css',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/minimize-button/down.txt" => '0',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/minimize-button/down.txt?fx" => 'https://core.parts/os-95/programs/locate-/window-/window-controls/minimize-button/down-fx.uri',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/minimize-button/layout.css?down" => 'https://core.parts/os-95/programs/locate-/window-/window-controls/minimize-button/down.txt',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/minimize-button/layout.css?constructor" => 'https://core.parts/os-95/programs/locate-/window-/window-controls/minimize-button/layout.css.c.js',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/minimize-button/layout.css.c.js" => 'return `
  :host {
   position: relative;
   width: 16px;
   height: 14px;
   background: #c3c3c3;
   box-shadow: ${(""+down) === \'1\' ? \'inset -1px -1px white, inset 1px 1px black, inset -2px -2px #dbdbdb, inset 2px 2px #7a7a7a\' : \'inset -1px -1px black, inset 1px 1px white, inset -2px -2px #7a7a7a, inset 2px 2px #dbdbdb\'}
  }
  :host::before {
   --color: black;
   display: block;
   position: absolute;
   content: "";
   width: 6px;
   height: 2px;
   background: var(--color);
   top: 9px;
   left: 4px }
  :host(:hover)::before {
   --color: blue
  }`',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/minimize-button/onclick.js" => '()=>{Ω[\'https://core.parts/os-95/programs/locate-/window-/minimized.txt\'] = \'1\'
  }',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/minimize-button/onpointerdown.js" => 'e => { e.stopPropagation(); Ω[\'https://core.parts/os-95/programs/locate-/window-/window-controls/minimize-button/down.txt\'] = \'1\'; Ω[\'https://core.parts/behaviors/release/src.uri\'] = \'https://core.parts/os-95/programs/locate-/window-/window-controls/minimize-button/release.js\'
  }',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/minimize-button/release.js" => 'e => { Ω[\'https://core.parts/os-95/programs/locate-/window-/window-controls/minimize-button/down.txt\'] = \'0\'
  }',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/minimize-button/?layout" => 'https://core.parts/os-95/programs/locate-/window-/window-controls/minimize-button/layout.css',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/minimize-button/?onclick" => 'https://core.parts/os-95/programs/locate-/window-/window-controls/minimize-button/onclick.js',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/minimize-button/?onpointerdown" => 'https://core.parts/os-95/programs/locate-/window-/window-controls/minimize-button/onpointerdown.js',
 "https://core.parts/os-95/programs/locate-/window-/minimized.txt" => '0',
 "https://core.parts/os-95/programs/locate-/window-/minimized.txt?fx" => 'https://core.parts/os-95/programs/locate-/window-/minimized/fx.uri',
 "https://core.parts/os-95/programs/locate-/window-/minimized/fx.uri" => 'https://core.parts/os-95/manifest.uri https://core.parts/os-95/programs/locate-/window-/active.txt https://core.parts/os-95/programs/locate-/task-/onpointerdown.js',
 "https://core.parts/os-95/programs/locate-/window-/onfocus.js?active" => 'https://core.parts/os-95/programs/locate-/window-/active.txt',
 "https://core.parts/os-95/programs/locate-/window-/onfocus.js?window" => 'https://core.parts/os-95/programs/locate-/window-/',
 "https://core.parts/os-95/programs/locate-/window-/onfocus.js?core" => 'https://core.parts/os-95/programs/relate-/window-/onfocus.js',
 "https://core.parts/os-95/programs/locate-/window-/position.json" => '
  {
   "x": 136, "y": 118, "w": 412, "h": 245,
   "range": {
    "x": [-64, 512],
    "y": [-2, 256],
    "w": [96, 256],
    "h": [64, 128]
   }
  }',
 "https://core.parts/os-95/programs/locate-/window-/position.json?fx" => 'https://core.parts/os-95/programs/locate-/window-/position/fx.uri',
 "https://core.parts/os-95/programs/locate-/window-/position/fx.uri" => 'https://core.parts/os-95/programs/locate-/window-/layout.css',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/restore-button/down-fx.uri" => 'https://core.parts/os-95/programs/locate-/window-/window-controls/restore-button/layout.css',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/restore-button/down.txt" => '0',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/restore-button/down.txt?fx" => 'https://core.parts/os-95/programs/locate-/window-/window-controls/restore-button/down-fx.uri',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/restore-button/layout.css?down" => 'https://core.parts/os-95/programs/locate-/window-/window-controls/restore-button/down.txt',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/restore-button/layout.css?constructor" => 'https://core.parts/os-95/programs/locate-/window-/window-controls/restore-button/layout.css.c.js',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/restore-button/layout.css.c.js" => 'return `:host {
   position: relative;
   width: 16px;
   height: 14px;
   background: #c3c3c3;
   box-shadow: ${(""+down) === \'1\' ? \'inset -1px -1px white, inset 1px 1px black, inset -2px -2px #dbdbdb, inset 2px 2px #7a7a7a\' : \'inset -1px -1px black, inset 1px 1px white, inset -2px -2px #7a7a7a, inset 2px 2px #dbdbdb\'}
  }
  :host::before, :host::after {
   --color: black;
   display: block;
   position: absolute;
   content: "";
   width: 6px;
   height: 6px;
   top: 5px;
   left: 3px;
   box-shadow: inset 0 2px var(--color), inset 0 0 0 1px var(--color);
   background: #c3c3c3 }
  :host::before {
   top: 2px;
   left: 5px }
  :host(:hover)::before, :host(:hover)::after {
   --color: blue }`',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/restore-button/onclick.js" => '()=>Ω[\'https://core.parts/os-95/programs/locate-/window-/maximized.txt\'] = \'0\'',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/restore-button/onpointerdown.js" => 'e => { e.stopPropagation(); Ω[\'https://core.parts/os-95/programs/locate-/window-/window-controls/restore-button/down.txt\'] = \'1\'; Ω[\'https://core.parts/behaviors/release/src.uri\'] = \'https://core.parts/os-95/programs/locate-/window-/window-controls/restore-button/release.js\'
  }',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/restore-button/release.js" => 'e => { Ω[\'https://core.parts/os-95/programs/locate-/window-/window-controls/restore-button/down.txt\'] = \'0\'
  }',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/restore-button/?layout" => 'https://core.parts/os-95/programs/locate-/window-/window-controls/restore-button/layout.css',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/restore-button/?onclick" => 'https://core.parts/os-95/programs/locate-/window-/window-controls/restore-button/onclick.js',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/restore-button/?onpointerdown" => 'https://core.parts/os-95/programs/locate-/window-/window-controls/restore-button/onpointerdown.js',
 "https://core.parts/os-95/programs/locate-/window-/sort-order-fx.uri" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/files-/manifest.uri https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/manifest.uri https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/manifest-fx.uri',
 "https://core.parts/os-95/programs/locate-/window-/sort_order.json" => '
  {
   "size": false,
   "type": true,
   "name": false
  }',
 "https://core.parts/os-95/programs/locate-/window-/sort_order.json?fx" => 'https://core.parts/os-95/programs/locate-/window-/sort-order-fx.uri',
 "https://core.parts/os-95/programs/locate-/window-/status-bar/layout.css?file_count" => 'https://core.parts/os-95/programs/locate-/window-/status/file_count.txt',
 "https://core.parts/os-95/programs/locate-/window-/status-bar/layout.css?folder_count" => 'https://core.parts/os-95/programs/locate-/window-/status/folder_count.txt',
 "https://core.parts/os-95/programs/locate-/window-/status-bar/layout.css?kireji_count" => 'https://core.parts/os-95/programs/locate-/window-/status/kireji_count.txt',
 "https://core.parts/os-95/programs/locate-/window-/status-bar/layout.css?show_kireji" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/show_kireji.txt',
 "https://core.parts/os-95/programs/locate-/window-/status-bar/layout.css?constructor" => 'https://core.parts/os-95/programs/locate-/window-/status-bar/layout.css.c.js',
 "https://core.parts/os-95/programs/locate-/window-/status-bar/layout.css.c.js" => '
  const
   num_files = parseInt("" + file_count),
   has_files = !!num_files,
   num_folders = parseInt("" + folder_count),
   has_folders = !!num_folders,
   do_kireji = ("" + show_kireji) === "1",
   num_kireji = do_kireji ? parseInt("" + kireji_count) : undefined,
   has_kireji = do_kireji ? !!num_kireji : undefined,
   status_items = [];
  if (has_folders) status_items.push(`${folder_count} folder${num_folders === 1 ? "" : "s"}`)
  if (has_files) status_items.push(`${file_count} file${num_files === 1 ? "" : "s"}`)
  if (has_kireji) status_items.push(`${kireji_count} kireji`)
  return `
   :host {
    padding: 0 3px;
    height: 17px;
    box-shadow: inset -1px -1px white, inset 1px 1px #7a7a7a;
    display: flex;
    flex-flow: row nowrap;
    align-items: center;
   }
   :host::after {
    content: "${status_items.join(", ")}"
   }
  `;',
 "https://core.parts/os-95/programs/locate-/window-/status-bar/?layout" => 'https://core.parts/os-95/programs/locate-/window-/status-bar/layout.css',
 "https://core.parts/os-95/programs/locate-/window-/status/file_count.txt" => '0',
 "https://core.parts/os-95/programs/locate-/window-/status/file_count.txt?fx" => 'https://core.parts/os-95/programs/locate-/window-/status/fx.uri',
 "https://core.parts/os-95/programs/locate-/window-/status/folder_count.txt" => '5',
 "https://core.parts/os-95/programs/locate-/window-/status/folder_count.txt?fx" => 'https://core.parts/os-95/programs/locate-/window-/status/fx.uri',
 "https://core.parts/os-95/programs/locate-/window-/status/fx.uri" => 'https://core.parts/os-95/programs/locate-/window-/status-bar/layout.css',
 "https://core.parts/os-95/programs/locate-/window-/status/kireji_count.txt" => '0',
 "https://core.parts/os-95/programs/locate-/window-/status/kireji_count.txt?fx" => 'https://core.parts/os-95/programs/locate-/window-/status/fx.uri',
 "https://core.parts/os-95/programs/locate-/window-/title-bar/layout.css" => '
  :host {
   background: #7f7f7f;
   color: white;
   display: flex;
   flex-flow: row nowrap;
   align-items: center;
   gap: 3px;
   height: 18px;
   padding: 0px 2px;
   box-sizing: border-box;
  }
  app-icon {
   width: 16px;
   height: 16px
  }',
 "https://core.parts/os-95/programs/locate-/window-/title-bar/manifest.uri" => 'https://core.parts/os-95/icons/folder-icon/ https://core.parts/os-95/programs/locate-/app-label/ https://core.parts/flex-spacer/ https://core.parts/os-95/programs/locate-/window-/window-controls/',
 "https://core.parts/os-95/programs/locate-/window-/title-bar/ondblclick.js?maximized" => 'https://core.parts/os-95/programs/locate-/window-/maximized.txt',
 "https://core.parts/os-95/programs/locate-/window-/title-bar/ondblclick.js?constructor" => 'https://core.parts/os-95/programs/locate-/window-/title-bar/ondblclick.c.js',
 "https://core.parts/os-95/programs/locate-/window-/title-bar/ondblclick.c.js" => 'return `() => { Ω[\'https://core.parts/os-95/programs/locate-/window-/window-controls/${(""+maximized) === \'1\' ? \'restore\' : \'maximize\'}-button/onclick.js\']() }`',
 "https://core.parts/os-95/programs/locate-/window-/title-bar/?layout" => 'https://core.parts/os-95/programs/locate-/window-/title-bar/layout.css',
 "https://core.parts/os-95/programs/locate-/window-/title-bar/?manifest" => 'https://core.parts/os-95/programs/locate-/window-/title-bar/manifest.uri',
 "https://core.parts/os-95/programs/locate-/window-/title-bar/?ondblclick" => 'https://core.parts/os-95/programs/locate-/window-/title-bar/ondblclick.js',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/layout.css" => '
  :host {
   display: flex;
   flex-flow: row nowrap
  }',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/manifest.uri?maximized" => 'https://core.parts/os-95/programs/locate-/window-/maximized.txt',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/manifest.uri?constructor" => 'https://core.parts/os-95/programs/locate-/window-/window-controls/manifest.uri.c.js',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/manifest.uri.c.js" => 'return `https://core.parts/os-95/programs/locate-/window-/window-controls/minimize-button/ https://core.parts/os-95/programs/locate-/window-/window-controls/${(""+maximized) === \'1\' ? \'restore\' : \'maximize\'}-button/ https://core.parts/os-95/programs/locate-/window-/window-controls/exit-button/`',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/?layout" => 'https://core.parts/os-95/programs/locate-/window-/window-controls/layout.css',
 "https://core.parts/os-95/programs/locate-/window-/window-controls/?manifest" => 'https://core.parts/os-95/programs/locate-/window-/window-controls/manifest.uri',
 "https://core.parts/os-95/programs/locate-/window-/?layout" => 'https://core.parts/os-95/programs/locate-/window-/layout.css',
 "https://core.parts/os-95/programs/locate-/window-/?manifest" => 'https://core.parts/os-95/programs/locate-/window-/manifest.uri',
 "https://core.parts/os-95/programs/locate-/window-/?onfocus" => 'https://core.parts/os-95/programs/locate-/window-/onfocus.js',
 "https://core.parts/os-95/programs/relate-/app-icon/?layout" => 'https://core.parts/os-95/programs/relate-/app-icon/layout.css',
 "https://core.parts/os-95/programs/relate-/app-icon/layout.css" => '
  :host {
   --size: 16px;
   width: var(--size);
   height: var(--size);
   font-size: var(--size);
   line-height: var(--size);
  }
  :host::before {
   content: "🧬";
  }',
 "https://core.parts/os-95/programs/relate-/app-label/layout.css?constructor" => 'https://core.parts/os-95/programs/relate-/app-label/layout.css.c.js',
 "https://core.parts/os-95/programs/relate-/app-label/layout.css?address" => 'https://core.parts/os-95/programs/locate-/window-/address.uri',
 "https://core.parts/os-95/programs/relate-/app-label/layout.css.c.js" => '
  return `:host {
   margin: 0;
   height: 16px;
   vertical-align: center;
   text-overflow: ellipsis;
   overflow: clip;
  }
  :host::after {
   content: "Relate - ${address}";
   white-space: nowrap;
  }`',
 "https://core.parts/os-95/programs/relate-/app-label/?layout" => 'https://core.parts/os-95/programs/relate-/app-label/layout.css',
 "https://core.parts/os-95/programs/relate-/task-/datum.txt" => 'https://core.parts/os-95/programs/relate-/window-/active.txt',
 "https://core.parts/os-95/programs/relate-/task-/index.txt" => '3',
 "https://core.parts/os-95/programs/relate-/task-/index.txt?datum" => 'https://core.parts/os-95/programs/relate-/task-/datum.txt',
 "https://core.parts/os-95/programs/relate-/task-/index.txt?fx" => 'https://core.parts/os-95/programs/relate-/task-/index/fx.uri',
 "https://core.parts/os-95/programs/relate-/task-/index.txt?order" => 'https://core.parts/os-95/taskbar-/selected/fx.uri',
 "https://core.parts/os-95/programs/relate-/task-/index.txt?constructor" => 'https://core.parts/os-95/programs/relate-/task-/index.txt.c.js',
 "https://core.parts/os-95/programs/relate-/task-/index.txt.c.js" => 'return ""+(""+order).split(" ").indexOf(""+datum)',
 "https://core.parts/os-95/programs/relate-/task-/index/fx.uri" => 'https://core.parts/os-95/programs/relate-/window-/active.txt',
 "https://core.parts/os-95/programs/relate-/task-/layout.css?open" => 'https://core.parts/os-95/programs/relate-/window-/active.txt',
 "https://core.parts/os-95/programs/relate-/task-/layout.css?constructor" => 'https://core.parts/os-95/programs/relate-/task-/layout.css.c.js',
 "https://core.parts/os-95/programs/relate-/task-/layout.css.c.js" => '
  return `
   :host {
    position: relative;
    height: 100%;
    margin: 0;
    width: 160px;
    display: flex;
    flex-flow: row nowrap;
    gap: 3px;
    border: none;${("" + open) === "1" ? `
    font: bold 11px sans-serif` : ``};
    box-sizing: border-box;
    padding: ${("" + open) === "0" ? 3 : 4}px 2px 2px;
    text-align: left;
    box-shadow: ${("" + open) === "0" ? "inset -1px -1px black, inset 1px 1px white, inset -2px -2px #7a7a7a, inset 2px 2px #dbdbdb" : "inset -1px -1px white, inset 1px 1px black, inset -2px -2px #dbdbdb, inset 2px 2px #7a7a7a"}
   }
   :host(:focus)::after {
    border: 1px dotted black;
    content: "";
    position: absolute;
    margin: 3px;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    pointer-events: none;
   }
   ${(""+open) === "1" ? `
   :host > * {
    z-index: 3
   }
   :host::before {
    content: "";
    position: absolute;
    margin: 2px;
    border-top: 1px solid white;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    background-image:linear-gradient(45deg, white 25%, transparent 25%, transparent 75%, white 75%, white),linear-gradient(45deg, white 25%, transparent 25%, transparent 75%, white 75%, white);background-size: 2px 2px;background-position: 0 0, 1px 1px;
   }` : ``}
   app-icon {
    width: 16px;
    height: 16px
   }
  `;',
 "https://core.parts/os-95/programs/relate-/task-/manifest.uri" => 'https://core.parts/os-95/programs/relate-/app-icon/ https://core.parts/os-95/programs/relate-/app-label/',
 "https://core.parts/os-95/programs/relate-/task-/manifest.uri?open" => 'https://core.parts/os-95/programs/relate-/window-/active.txt',
 "https://core.parts/os-95/programs/relate-/task-/onpointerdown.js?minimized" => 'https://core.parts/os-95/programs/relate-/window-/minimized.txt',
 "https://core.parts/os-95/programs/relate-/task-/onpointerdown.js?active" => 'https://core.parts/os-95/programs/relate-/window-/active.txt',
 "https://core.parts/os-95/programs/relate-/task-/onpointerdown.js?window" => 'https://core.parts/os-95/programs/relate-/window-/',
 "https://core.parts/os-95/programs/relate-/task-/onpointerdown.js?constructor" => 'https://core.parts/os-95/programs/relate-/task-/onpointerdown.c.js',
 "https://core.parts/os-95/programs/relate-/task-/onpointerdown.c.js" => '
  const
   is_minimized = ("" + minimized) === "1",
   is_inactive = ("" + active) === "0",
   minimized_url = minimized.headerOf().href,
   active_url = active.headerOf().href,
   window_url = window.headerOf().href,
   put_in_front = `
    const
     windows_uri = "https://core.parts/os-95/windows.uri",
     windows_string = Ω[windows_uri].toPrimitive(),
     windows = windows_string ? windows_string.split(" ") : [],
     own_window = "${window_url}";
    if (windows.at(-1) !== own_window) {
     const window_index = windows.indexOf(own_window);
     if (window_index !== -1) windows.splice(window_index, 1)
     windows.push(own_window)
     Ω[windows_uri] = windows.join(" ")
    }`;
  return `
   e => {${ is_minimized ? `
    Ω["${minimized_url}"] = "0";
    Ω["${active_url}"] = "1";
    ${put_in_front}` : is_inactive ? `
    Ω["${active_url}"] = "1";
    ${put_in_front}` : `
    Ω["${active_url}"] = "0";
    Ω["${minimized_url}"] = "1";`}
   }
  `',
 "https://core.parts/os-95/programs/relate-/task-/open/fx.uri" => 'https://core.parts/os-95/programs/relate-/task-/layout.css https://core.parts/os-95/taskbar-/selected.txt https://core.parts/os-95/programs/relate-/window-/layout.css https://core.parts/os-95/programs/relate-/task-/onpointerdown.js',
 "https://core.parts/os-95/programs/relate-/task-/?layout" => 'https://core.parts/os-95/programs/relate-/task-/layout.css',
 "https://core.parts/os-95/programs/relate-/task-/?manifest" => 'https://core.parts/os-95/programs/relate-/task-/manifest.uri',
 "https://core.parts/os-95/programs/relate-/task-/?onpointerdown" => 'https://core.parts/os-95/programs/relate-/task-/onpointerdown.js',
 "https://core.parts/os-95/programs/relate-/start-menu-item/app-label/layout.css" => ':host::after {
   height: 24px;
   content: "Relate";
  }',
 "https://core.parts/os-95/programs/relate-/start-menu-item/app-label/?layout" => 'https://core.parts/os-95/programs/relate-/start-menu-item/app-label/layout.css',
 "https://core.parts/os-95/programs/relate-/start-menu-item/layout.css" => '
  :host {
   position: relative;
   display: flex;
   flex-flow: row nowrap;
   align-items: center;
   padding: 4px 0 }
  :host(:hover) {
   background: #00007f;
   color: white }
  app-icon {
   width: 24px;
   height: 24px;
   margin: 0 10px;
   --size: 24px;
  }',
 "https://core.parts/os-95/programs/relate-/start-menu-item/manifest.uri" => 'https://core.parts/os-95/programs/relate-/app-icon/ https://core.parts/os-95/programs/relate-/start-menu-item/app-label/',
 "https://core.parts/os-95/programs/relate-/start-menu-item/onclick.js" => '
  e => {
   // Ω["https://core.parts/os-95/taskbar-/selected.txt"] = "" + Ω["https://core.parts/os-95/programs/relate-/task-/index.txt"]
   Ω["https://core.parts/os-95/programs/relate-/task-/"].onfocus(e)
  }',
 "https://core.parts/os-95/programs/relate-/start-menu-item/?layout" => 'https://core.parts/os-95/programs/relate-/start-menu-item/layout.css',
 "https://core.parts/os-95/programs/relate-/start-menu-item/?manifest" => 'https://core.parts/os-95/programs/relate-/start-menu-item/manifest.uri',
 "https://core.parts/os-95/programs/relate-/start-menu-item/?onclick" => 'https://core.parts/os-95/programs/relate-/task-/onpointerdown.js',
 "https://core.parts/os-95/programs/relate-/window-/active.txt" => '0',
 "https://core.parts/os-95/programs/relate-/window-/active.txt?fx" => 'https://core.parts/os-95/programs/relate-/task-/open/fx.uri',
 "https://core.parts/os-95/programs/relate-/window-/active.txt?index" => 'https://core.parts/os-95/programs/relate-/task-/index.txt',
 "https://core.parts/os-95/programs/relate-/window-/active.txt?minimized" => 'https://core.parts/os-95/programs/relate-/window-/minimized.txt',
 "https://core.parts/os-95/programs/relate-/window-/active.txt?selected" => 'https://core.parts/os-95/taskbar-/selected.txt',
 "https://core.parts/os-95/programs/relate-/window-/active.txt?constructor" => 'https://core.parts/os-95/programs/relate-/window-/active.txt.c.js',
 "https://core.parts/os-95/programs/relate-/window-/active.txt.c.js" => 'return ("" + minimized) === \'1\' ? \'0\' : ("" + selected) === ("" + index) ? \'1\' : \'0\'',
 "https://core.parts/os-95/programs/relate-/window-/address.uri" => 'https://core.parts/os-95/programs/relate-/window-/graph-/',
 "https://core.parts/os-95/programs/relate-/window-/address.uri?fx" => 'https://core.parts/os-95/programs/relate-/window-/readdress.uri',
 "https://core.parts/os-95/programs/relate-/window-/readdress.uri" => 'https://core.parts/os-95/programs/relate-/window-/graph-/manifest.uri https://core.parts/os-95/programs/relate-/app-label/layout.css',
 "https://core.parts/os-95/programs/relate-/window-/exit-button/layout.css" => '
  :host {
   position: relative;
   width: 16px;
   height: 14px;
   background: #c3c3c3;
   margin-left: 2px;
   box-shadow: inset -1px -1px black, inset 1px 1px white, inset -2px -2px #7a7a7a, inset 2px 2px #dbdbdb }
  :host::before, :host::after {
   --color: #7f7f7f;
   content: "";
   display: block;
   position: absolute;
   width: 8px;
   height: 7px;
   left: 4px;
   top: 3px;
   background: linear-gradient(to top left, transparent 0%, transparent calc(50% - 1px), var(--color) calc(50% - 1px), var(--color) calc(50% + 1px),  transparent calc(50% + 1px),  transparent 100%), linear-gradient(to top right,  transparent 0%,  transparent calc(50% - 1px), var(--color) calc(50% - 1px), var(--color) calc(50% + 1px),  transparent calc(50% + 1px),  transparent 100%) }
  :host::before {
   --color: white;
   left: 5px;
   top: 4px
  }',
 "https://core.parts/os-95/programs/relate-/window-/exit-button/?layout" => 'https://core.parts/os-95/programs/relate-/window-/exit-button/layout.css',
 "https://core.parts/os-95/programs/relate-/window-/graph-/?manifest" => 'https://core.parts/os-95/programs/relate-/window-/graph-/manifest.uri',
 "https://core.parts/os-95/programs/relate-/window-/graph-/?layout" => 'https://core.parts/os-95/programs/relate-/window-/graph-/layout.css',
 "https://core.parts/os-95/programs/relate-/window-/graph-/layout.css?white_grid" => 'https://core.parts/img/white-grid.png',
 "https://core.parts/os-95/programs/relate-/window-/graph-/layout.css?blue_grid" => 'https://core.parts/img/blue-grid.png',
 "https://core.parts/os-95/programs/relate-/window-/graph-/manifest.uri?address" => 'https://core.parts/os-95/programs/relate-/window-/address.uri',
 "https://core.parts/os-95/programs/relate-/window-/graph-/manifest.uri?transform" => 'https://core.parts/components/transform-/construct.js',
 "https://core.parts/os-95/programs/relate-/window-/graph-/manifest.uri?constructor" => 'https://core.parts/os-95/programs/relate-/window-/graph-/manifest.uri.c.js',
 "https://core.parts/os-95/programs/relate-/window-/graph-/manifest.uri.c.js" => '
  const
   core_url = "https://core.parts/os-95/programs/relate-/core-node/",
   kireji_urls = new Set(),
   own_url = "" + address;
  for (const url in Δ) {
   if (!url.match(/^[^?]*\?\w*$/)) continue
   if (!kireji_urls.has(url) && own_url === url.split("?")[0]) kireji_urls.add(Δ[url])
  }
  return [own_url, ...kireji_urls].map(address => {
   const
    graph_url = "https://core.parts/os-95/programs/relate-/window-/graph-/",
    node_url = `${graph_url}${hash(own_url + " " + address)}/node-/`,
    transform_url = node_url + "transform-/",
    position_url = `${node_url}position.json`;
   Ω[`${node_url}?core`] = core_url
   Ω[`${node_url}?layout`] = `${node_url}layout.css`
   Ω[`${node_url}?onpointerdown`] = `${node_url}onpointerdown.js`
   Ω[`${node_url}?manifest`] = `${node_url}manifest.uri`
   Ω[`${node_url}layout.css?core`] = `${core_url}layout.css`
   Ω[`${node_url}layout.css?position`] = position_url
   Ω[`${node_url}layout.css?graph_position`] = `${graph_url}position.json`
   Ω[`${node_url}onpointerdown.js?core`] = `${core_url}onpointerdown.js`
   Ω[`${node_url}onpointerdown.js?position`] = position_url
   Ω[`${node_url}manifest.uri?core`] = `${core_url}manifest.uri`
   Ω[`${node_url}manifest.uri?node`] = node_url
   Ω[`${node_url}manifest.uri?proxy`] = address
   Ω[`${node_url}position.json?core`] = `${core_url}position.json`
   Ω[`${node_url}position.json?fx`] = `${node_url}reposition.uri`
   Ω[`${node_url}reposition.uri`] = `${node_url}layout.css`
   transform(transform_url, position_url, "ew")
   return node_url
  }).join(" ")',
 "https://core.parts/os-95/programs/relate-/window-/graph-/?onpointerdown" => 'https://core.parts/os-95/programs/relate-/window-/graph-/onpointerdown.js',
 "https://core.parts/os-95/programs/relate-/window-/graph-/onpointerdown.js?core" => 'https://core.parts/behaviors/resize/onpointerdown.js',
 "https://core.parts/os-95/programs/relate-/window-/graph-/onpointerdown.js?mode" => 'https://core.parts/behaviors/move.txt',
 "https://core.parts/os-95/programs/relate-/window-/graph-/onpointerdown.js?position" => 'https://core.parts/os-95/programs/relate-/window-/graph-/position.json',
 "https://core.parts/os-95/programs/relate-/window-/graph-/position.json?fx" => 'https://core.parts/os-95/programs/relate-/window-/graph-/reposition.uri',
 "https://core.parts/os-95/programs/relate-/window-/graph-/reposition.uri" => 'https://core.parts/os-95/programs/relate-/window-/graph-/layout.css',
 "https://core.parts/os-95/programs/relate-/window-/graph-/position.json" => '{}',
 "https://core.parts/os-95/programs/relate-/window-/graph-/layout.css?position" => 'https://core.parts/os-95/programs/relate-/window-/graph-/position.json',
 "https://core.parts/os-95/programs/relate-/window-/graph-/layout.css?constructor" => 'https://core.parts/os-95/programs/relate-/window-/graph-/layout.css.c.js',
 "https://core.parts/os-95/programs/relate-/window-/graph-/layout.css.c.js" => '
  const { x = 0, y = 0 } = JSON.parse("" + position)
  return `
   :host {
    --size: 18px;
    --paper-color: #c3c3c3;;
    --focus-color: rgb(0, 0, 163);
    --graph-x: ${x}px;
    --graph-y: ${y}px;
    --white-grid: url("data:image/png;base64,${white_grid}");
    --blue-grid: url("data:image/png;base64,${blue_grid}");
    --halftone-a: black;
    --halftone-b: transparent;
    --halftone-size: 2px;
    --halftone:
     linear-gradient(
      45deg,
      var(--halftone-a) 25%,
      var(--halftone-b) 25%,
      var(--halftone-b) 75%,
      var(--halftone-a) 75%,
      var(--halftone-a)
     ) calc(50% + ${x}px) calc(50% + ${y}px) / var(--halftone-size) var(--halftone-size),
     linear-gradient(
      45deg,
      var(--halftone-a) 25%,
      var(--halftone-b) 25%,
      var(--halftone-b) 75%,
      var(--halftone-a) 75%,
      var(--halftone-a)
     ) calc(var(--halftone-size) / 2 + 50% + ${x}px) calc(var(--halftone-size) / 2 + 50% + ${y}px) / var(--halftone-size) var(--halftone-size);
    position: relative;
    flex: 1 1;
    box-shadow:
     -0.5px -0.5px 0 0.5px black,
     0 0 0 1px #dbdbdb,
     -0.5px -0.5px 0 1.5px #7a7a7a,
     0 0 0 2px white;
    background: var(--paper-color);
    margin: 2px;
    display: grid;
    overflow: clip;
    height: 100%;
    /* cursor: all-scroll */;
    background: var(--halftone);
    --halftone-size: calc(var(--size) / 8);
    --halftone-a: #334246ff;
    --halftone-b: #3342467f;
    cursor: url(\'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" style="font-size: 32px; line-height: 32px"><text y="28">🥢</text></svg>\') 2 30, pointer;
   }
   :host > * {
    --size: inherit;
    --paper-color: inherit;
   }
  `',
 "https://core.parts/os-95/programs/relate-/cathode-/?core" => 'https://core.parts/os-95/programs/relate-/electrode-/',
 "https://core.parts/os-95/programs/relate-/anode-/?core" => 'https://core.parts/os-95/programs/relate-/electrode-/',
 "https://core.parts/os-95/programs/relate-/electrode-/?layout" => 'https://core.parts/os-95/programs/relate-/electrode-/layout.css',
 "https://core.parts/os-95/programs/relate-/electrode-/layout.css" => '
  :host {
   --overlay: transparent;
   display: inline-block;
   width: var(--size);
   height: var(--size);
   background-image:
    linear-gradient(45deg, var(--overlay) 25%, transparent 25%, transparent 75%, var(--overlay) 75%, var(--overlay)),
    linear-gradient(45deg, var(--overlay) 25%, transparent 25%, transparent 75%, var(--overlay) 75%, var(--overlay));
   background-size: 2px 2px;
   background-position: 0 0, 1px 1px;
  }
  :host(:hover) {
   --overlay: yellow;
   cursor: url(\'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" style="font-size: 32px; line-height: 32px"><text y="32">✍</text></svg>\') 1 30, pointer;
  }',
 "https://core.parts/os-95/programs/relate-/core-node/onpointerdown.js?core" => 'https://core.parts/behaviors/resize/onpointerdown.js',
 "https://core.parts/os-95/programs/relate-/core-node/onpointerdown.js?mode" => 'https://core.parts/behaviors/move.txt',
 "https://core.parts/os-95/programs/relate-/core-node/onpointerdown.js?stop_propagation" => 'https://core.parts/const/one.txt',
 "https://core.parts/os-95/programs/relate-/core-node/onpointerdown.js?should_focus" => 'https://core.parts/const/one.txt',
 "https://core.parts/os-95/programs/relate-/core-node/manifest.uri?cell" => 'https://core.parts/components/cell-/construct.js',
 "https://core.parts/os-95/programs/relate-/core-node/manifest.uri?label_css" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/label_css.js',
 "https://core.parts/os-95/programs/relate-/core-node/manifest.uri?item_layout" => 'https://core.parts/os-95/programs/locate-/window-/explorer-view/header-/item_layout.css',
 "https://core.parts/os-95/programs/relate-/core-node/manifest.uri?constructor" => 'https://core.parts/os-95/programs/relate-/core-node/manifest.c.js',
 "https://core.parts/os-95/programs/relate-/core-node/manifest.c.js" => '
  const
   { href, groups: { type } } = proxy.headerOf(),
   is_index = href.endsWith("/"),
   crumbs = href.replace(/^https:\/\//,"").split("/"),
   path_length = crumbs.length,
   file_crumb_index = path_length - (1 + is_index),
   path = crumbs.slice(0, file_crumb_index - 1 + is_index).join("/") + "/",
   filename = crumbs[file_crumb_index],
   common_label = `:host {
    margin: 0 calc(var(--size) / 8);
    line-height: inherit;
   }`,
   common_item = `:host {
    display: flex;
    flex-flow: row nowrap;
    height: var(--size);
    align-items: center;
    line-height: var(--size);
   }`,
   cathode_url = "https://core.parts/os-95/programs/relate-/cathode-/",
   anode_url = "https://core.parts/os-95/programs/relate-/anode-/",
   node_url = node.headerOf().href,
   title_url = node_url + "title-bar/",
   icon_url = "https://core.parts/os-95/icons/" + type.replace(/[^a-zA-Z0-9]+/g, "-") + "-icon/",
   label_url = node_url + "title-bar/label-/",
   path_url = node_url + "title-bar/path-/",
   resize_left_url = node_url + "transform-/left-/",
   resize_right_url = node_url + "transform-/right-/"

  cell(label_url, `
  ${label_css(filename)}
  ${common_label}
  :host {
   margin-left: 0;
  }`);

  cell(path_url, `
  ${label_css(path)}
  ${common_label}
  :host {
   font-weight: normal;
   flex: 1 1;
   margin-right: 0;
  }`);

  cell(title_url, `
  ${item_layout}
  ${common_item}
  :host {
   text-align: right;
   position: relative;
   box-sizing: border-box;
   justify-content: end;
   height: calc(1.5 * var(--size));
   padding: calc(var(--size) / 2);
   padding-bottom: 0;
   align-items: stretch;
   font-weight: bold;
  }
  :host > :nth-child(2) {
   --size: inherit;
  }`, [path_url, label_url, icon_url].join(" ") );
  const keys = new Set()
  for (const url in Δ) {
   if (!url.match(/^[^?]*\?\w*$/)) continue
   const [base, π] = url.split("?")
   if (keys.has(π)) continue;
   if (href === base) { keys.add(π) }
  }
  return [title_url, "https://core.parts/os-95/horizontal-line/", ...[...keys].map(kireji => {
   const
    cell_url = node_url + hash(`${href}?${kireji}`) + "/kireji-/",
    label_url = cell_url + "label-/";
   cell(label_url, `
    ${label_css(kireji)}
    ${common_label}
    :host {
     margin-left: 0;
     box-sizing: border-box;
     flex: 1 1;
    }`);
   cell(cell_url, `
    ${item_layout}
    ${common_item}`, cathode_url + " " + label_url);
   return cell_url
  }), anode_url, resize_left_url, resize_right_url].join(" ")',
 "https://core.parts/os-95/programs/relate-/core-node/position.json?constructor" => 'https://core.parts/os-95/programs/relate-/core-node/position.json.c.js',
 "https://core.parts/os-95/programs/relate-/core-node/position.json.c.js" => 'return JSON.stringify({ w: 16 * 18, range: { w: [5 * 18] }, snap: { x: 9, y: 9 } })',
 "https://core.parts/os-95/programs/relate-/core-node/layout.css?constructor" => 'https://core.parts/os-95/programs/relate-/core-node/layout.css.c.js',
 "https://core.parts/os-95/programs/relate-/core-node/layout.css.c.js" => '
  const { x = 0, y = 0, w = 0 } = JSON.parse("" + position), { x: graphX = 0, y: graphY = 0 } = JSON.parse("" + graph_position)
  return `
   :host {
    position: absolute;
    top: calc(50% + ${y}px + var(--graph-y));
    left: calc(50% + ${x}px + var(--graph-x));
    width: ${w}px;
    display: flex;
    flex-flow: column nowrap;
    background: #c3c3c3;
    padding-bottom: calc(var(--size) / 2);
    border-radius: calc(var(--size) / 8);
    box-shadow:
     0.5px 0,
     0 0.5px,
     0 -0.5px,
     -0.5px 0,
     inset 0.5px 0,
     inset 0 0.5px,
     inset 0 -0.5px,
     inset -0.5px 0;
   }
   anode- {
    position: absolute;
    right: calc(var(--size) / 2);
    /* top: 0; */
    top: calc(var(--size) / 2);
   }
   horizontal-line {
    margin: calc((var(--size) / 2) - 1px) 0;
   }
   :host(:focus) {
    outline: none;
    background-image: linear-gradient(45deg, white 25%, transparent 25%, transparent 75%, white 75%, white), linear-gradient(45deg, white 25%, transparent 25%, transparent 75%, white 75%, white);
    background-size: 2px 2px;
    background-position: 0 0, 1px 1px;
   }
   :host > title-bar {
    --bg: #7f7f7f;
   }
   :host(:focus) > title-bar {
    --bg: var(--focus-color);
   }
  `',
 "https://core.parts/os-95/programs/relate-/window-/layout.css?active" => 'https://core.parts/os-95/programs/relate-/window-/active.txt',
 "https://core.parts/os-95/programs/relate-/window-/layout.css?maximized" => 'https://core.parts/os-95/programs/relate-/window-/maximized.txt',
 "https://core.parts/os-95/programs/relate-/window-/layout.css?position" => 'https://core.parts/os-95/programs/relate-/window-/position.json',
 "https://core.parts/os-95/programs/relate-/window-/layout.css?constructor" => 'https://core.parts/os-95/programs/relate-/window-/layout.css.c.js',
 "https://core.parts/os-95/programs/relate-/window-/layout.css.c.js" => '
  const common = "position: absolute; display: flex; flex-flow: column nowrap; gap: 2px; background: #c3c3c3; box-sizing: border-box", titlebar = ("" + active) === "1" ? `title-bar { background: rgb(0, 0, 163); }` : ``
  if (("" + maximized) === \'1\') {
   return `
    :host {
     position: absolute;
     top: 0;
     left: 0;
     right: 0;
     bottom: 28px;
     padding: 2px;
     ${common}
    } ${titlebar}`
  } else {
   const { x = 0, y = 0, w = 0, h = 0 } = JSON.parse("" + position);
   return `
    :host {
     width: ${w}px;
     height: ${h}px;
     left: ${x}px;
     top: ${y}px;
     min-height: fit-content;
     padding: 4px;
     background: #c3c3c3;
     box-shadow:
      inset -1px -1px black,
      inset 1px 1px #c3c3c3,
      inset -2px -2px #7a7a7a,
      inset 2px 2px white,
      5px 7px 3px #0002;
     ${common}
    }
    ${titlebar}`
  }',
 "https://core.parts/os-95/programs/relate-/window-/manifest.uri?title" => 'https://core.parts/os-95/programs/relate-/window-/title-bar/',
 "https://core.parts/os-95/programs/relate-/window-/manifest.uri?graph" => 'https://core.parts/os-95/programs/relate-/window-/graph-/',
 "https://core.parts/os-95/programs/relate-/window-/manifest.uri?transform_path" => 'https://core.parts/os-95/programs/relate-/window-/transform-/',
 "https://core.parts/os-95/programs/relate-/window-/manifest.uri?transform" => 'https://core.parts/components/transform-/construct.js',
 "https://core.parts/os-95/programs/relate-/window-/manifest.uri?position" => 'https://core.parts/os-95/programs/relate-/window-/position.json',
 "https://core.parts/os-95/programs/relate-/window-/manifest.uri?constructor" => 'https://core.parts/os-95/programs/relate-/window-/manifest.uri.c.js',
 "https://core.parts/os-95/programs/relate-/window-/manifest.uri.c.js" => <<<JS
  const [title_url, graph_url, transform_url, position_url] = [title, graph, transform_path, position].map(x => x.headerOf().href)
  const transform_urls = transform(transform_url, position_url, "nesw", title_url);
  return [title_url, graph_url, transform_urls].join(" ")
  JS,
 "https://core.parts/os-95/programs/relate-/window-/maximize-button/down-fx.uri" => 'https://core.parts/os-95/programs/relate-/window-/maximize-button/layout.css',
 "https://core.parts/os-95/programs/relate-/window-/maximize-button/down.txt" => '0',
 "https://core.parts/os-95/programs/relate-/window-/maximize-button/down.txt?fx" => 'https://core.parts/os-95/programs/relate-/window-/maximize-button/down-fx.uri',
 "https://core.parts/os-95/programs/relate-/window-/maximize-button/layout.css?down" => 'https://core.parts/os-95/programs/relate-/window-/maximize-button/down.txt',
 "https://core.parts/os-95/programs/relate-/window-/maximize-button/layout.css?constructor" => 'https://core.parts/os-95/programs/relate-/window-/maximize-button/layout.css.c.js',
 "https://core.parts/os-95/programs/relate-/window-/maximize-button/layout.css.c.js" => 'return `:host {
   position: relative;
   width: 16px;
   height: 14px;
   background: #c3c3c3;
   box-shadow: ${(""+down) === \'1\' ? \'inset -1px -1px white, inset 1px 1px black, inset -2px -2px #dbdbdb, inset 2px 2px #7a7a7a\' : \'inset -1px -1px black, inset 1px 1px white, inset -2px -2px #7a7a7a, inset 2px 2px #dbdbdb\'}
  }
  :host::before {
   --color: black;
   display: block;
   position: absolute;
   content: "";
   width: 9px;
   height: 9px;
   top: 2px;
   left: 3px;
   box-shadow: inset 0 2px var(--color), inset 0 0 0 1px var(--color) }
  :host(:hover)::before {
   --color: blue }`',
 "https://core.parts/os-95/programs/relate-/window-/maximize-button/onclick.js" => '
  () => {
   Ω[\'https://core.parts/os-95/programs/relate-/window-/maximized.txt\'] = \'1\'
  }',
 "https://core.parts/os-95/programs/relate-/window-/maximize-button/onpointerdown.js" => '
  e => {
   e.stopPropagation(); Ω[\'https://core.parts/os-95/programs/relate-/window-/maximize-button/down.txt\'] = \'1\'
   Ω[\'https://core.parts/behaviors/release/src.uri\'] = \'https://core.parts/os-95/programs/relate-/window-/maximize-button/release.js\'
  }',
 "https://core.parts/os-95/programs/relate-/window-/maximize-button/release.js" => '
  e => {
   Ω[\'https://core.parts/os-95/programs/relate-/window-/maximize-button/down.txt\'] = \'0\'
  }',
 "https://core.parts/os-95/programs/relate-/window-/maximize-button/?layout" => 'https://core.parts/os-95/programs/relate-/window-/maximize-button/layout.css',
 "https://core.parts/os-95/programs/relate-/window-/maximize-button/?onclick" => 'https://core.parts/os-95/programs/relate-/window-/maximize-button/onclick.js',
 "https://core.parts/os-95/programs/relate-/window-/maximize-button/?onpointerdown" => 'https://core.parts/os-95/programs/relate-/window-/maximize-button/onpointerdown.js',
 "https://core.parts/os-95/programs/relate-/window-/maximized.txt" => '0',
 "https://core.parts/os-95/programs/relate-/window-/maximized.txt?fx" => 'https://core.parts/os-95/programs/relate-/window-/maximized/fx.uri',
 "https://core.parts/os-95/programs/relate-/window-/maximized/fx.uri" => 'https://core.parts/os-95/programs/relate-/window-/layout.css https://core.parts/os-95/programs/relate-/window-/window-controls/manifest.uri https://core.parts/os-95/programs/relate-/window-/title-bar/ondblclick.js',
 "https://core.parts/os-95/programs/relate-/window-/minimize-button/down-fx.uri" => 'https://core.parts/os-95/programs/relate-/window-/minimize-button/layout.css',
 "https://core.parts/os-95/programs/relate-/window-/minimize-button/down.txt" => '0',
 "https://core.parts/os-95/programs/relate-/window-/minimize-button/down.txt?fx" => 'https://core.parts/os-95/programs/relate-/window-/minimize-button/down-fx.uri',
 "https://core.parts/os-95/programs/relate-/window-/minimize-button/layout.css?down" => 'https://core.parts/os-95/programs/relate-/window-/minimize-button/down.txt',
 "https://core.parts/os-95/programs/relate-/window-/minimize-button/layout.css?constructor" => 'https://core.parts/os-95/programs/relate-/window-/minimize-button/layout.css.c.js',
 "https://core.parts/os-95/programs/relate-/window-/minimize-button/layout.css.c.js" => 'return `:host {
   position: relative;
   width: 16px;
   height: 14px;
   background: #c3c3c3;
   box-shadow: ${(""+down) === \'1\' ? \'inset -1px -1px white, inset 1px 1px black, inset -2px -2px #dbdbdb, inset 2px 2px #7a7a7a\' : \'inset -1px -1px black, inset 1px 1px white, inset -2px -2px #7a7a7a, inset 2px 2px #dbdbdb\'}
  }
  :host::before {
   --color: black;
   display: block;
   position: absolute;
   content: "";
   width: 6px;
   height: 2px;
   background: var(--color);
   top: 9px;
   left: 4px }
  :host(:hover)::before {
   --color: blue }`',
 "https://core.parts/os-95/programs/relate-/window-/minimize-button/onclick.js" => '()=>{Ω[\'https://core.parts/os-95/programs/relate-/window-/minimized.txt\'] = \'1\'
  }',
 "https://core.parts/os-95/programs/relate-/window-/minimize-button/onpointerdown.js" => 'e => { e.stopPropagation(); Ω[\'https://core.parts/os-95/programs/relate-/window-/minimize-button/down.txt\'] = \'1\'; Ω[\'https://core.parts/behaviors/release/src.uri\'] = \'https://core.parts/os-95/programs/relate-/window-/minimize-button/release.js\'
  }',
 "https://core.parts/os-95/programs/relate-/window-/minimize-button/release.js" => 'e => { Ω[\'https://core.parts/os-95/programs/relate-/window-/minimize-button/down.txt\'] = \'0\'
  }',
 "https://core.parts/os-95/programs/relate-/window-/minimize-button/?layout" => 'https://core.parts/os-95/programs/relate-/window-/minimize-button/layout.css',
 "https://core.parts/os-95/programs/relate-/window-/minimize-button/?onclick" => 'https://core.parts/os-95/programs/relate-/window-/minimize-button/onclick.js',
 "https://core.parts/os-95/programs/relate-/window-/minimize-button/?onpointerdown" => 'https://core.parts/os-95/programs/relate-/window-/minimize-button/onpointerdown.js',
 "https://core.parts/os-95/programs/relate-/window-/minimized.txt" => '0',
 "https://core.parts/os-95/programs/relate-/window-/minimized.txt?fx" => 'https://core.parts/os-95/programs/relate-/window-/minimized/fx.uri',
 "https://core.parts/os-95/programs/relate-/window-/minimized/fx.uri" => 'https://core.parts/os-95/manifest.uri https://core.parts/os-95/programs/relate-/window-/active.txt https://core.parts/os-95/programs/relate-/task-/onpointerdown.js',
 "https://core.parts/os-95/programs/relate-/window-/onfocus.js?active" => 'https://core.parts/os-95/programs/relate-/window-/active.txt',
 "https://core.parts/os-95/programs/relate-/window-/onfocus.js?window" => 'https://core.parts/os-95/programs/relate-/window-/',
 "https://core.parts/os-95/programs/relate-/window-/onfocus.js?constructor" => 'https://core.parts/behaviors/window-focus.c.js',
 "https://core.parts/os-95/programs/relate-/window-/position.json" => '
  {
   "x": 128,
   "y": 128,
   "w": 256,
   "h": 256,
   "range": {
    "x": [-64, 512],
    "y": [-2, 256],
    "w": [96, 256],
    "h": [64, 128]
   }
  }',
 "https://core.parts/os-95/programs/relate-/window-/position.json?fx" => 'https://core.parts/os-95/programs/relate-/window-/position/fx.uri',
 "https://core.parts/os-95/programs/relate-/window-/position/fx.uri" => 'https://core.parts/os-95/programs/relate-/window-/layout.css',
 "https://core.parts/os-95/programs/relate-/window-/restore-button/down-fx.uri" => 'https://core.parts/os-95/programs/relate-/window-/restore-button/layout.css',
 "https://core.parts/os-95/programs/relate-/window-/restore-button/down.txt" => '0',
 "https://core.parts/os-95/programs/relate-/window-/restore-button/down.txt?fx" => 'https://core.parts/os-95/programs/relate-/window-/restore-button/down-fx.uri',
 "https://core.parts/os-95/programs/relate-/window-/restore-button/layout.css?down" => 'https://core.parts/os-95/programs/relate-/window-/restore-button/down.txt',
 "https://core.parts/os-95/programs/relate-/window-/restore-button/layout.css?constructor" => 'https://core.parts/os-95/programs/relate-/window-/restore-button/layout.css.c.js',
 "https://core.parts/os-95/programs/relate-/window-/restore-button/layout.css.c.js" => 'return `:host {
   position: relative;
   width: 16px;
   height: 14px;
   background: #c3c3c3;
   box-shadow: ${(""+down) === \'1\' ? \'inset -1px -1px white, inset 1px 1px black, inset -2px -2px #dbdbdb, inset 2px 2px #7a7a7a\' : \'inset -1px -1px black, inset 1px 1px white, inset -2px -2px #7a7a7a, inset 2px 2px #dbdbdb\'}
  }
  :host::before, :host::after {
   --color: black;
   display: block;
   position: absolute;
   content: "";
   width: 6px;
   height: 6px;
   top: 5px;
   left: 3px;
   box-shadow: inset 0 2px var(--color), inset 0 0 0 1px var(--color);
   background: #c3c3c3 }
  :host::before {
   top: 2px;
   left: 5px }
  :host(:hover)::before, :host(:hover)::after {
   --color: blue }`',
 "https://core.parts/os-95/programs/relate-/window-/restore-button/onclick.js" => '()=>Ω[\'https://core.parts/os-95/programs/relate-/window-/maximized.txt\'] = \'0\'',
 "https://core.parts/os-95/programs/relate-/window-/restore-button/onpointerdown.js" => 'e => { e.stopPropagation(); Ω[\'https://core.parts/os-95/programs/relate-/window-/restore-button/down.txt\'] = \'1\'; Ω[\'https://core.parts/behaviors/release/src.uri\'] = \'https://core.parts/os-95/programs/relate-/window-/restore-button/release.js\'
  }',
 "https://core.parts/os-95/programs/relate-/window-/restore-button/release.js" => 'e => { Ω[\'https://core.parts/os-95/programs/relate-/window-/restore-button/down.txt\'] = \'0\'
  }',
 "https://core.parts/os-95/programs/relate-/window-/restore-button/?layout" => 'https://core.parts/os-95/programs/relate-/window-/restore-button/layout.css',
 "https://core.parts/os-95/programs/relate-/window-/restore-button/?onclick" => 'https://core.parts/os-95/programs/relate-/window-/restore-button/onclick.js',
 "https://core.parts/os-95/programs/relate-/window-/restore-button/?onpointerdown" => 'https://core.parts/os-95/programs/relate-/window-/restore-button/onpointerdown.js',
 "https://core.parts/os-95/programs/relate-/window-/title-bar/layout.css?focus" => 'https://core.parts/os-95/programs/relate-/window-/active.txt',
 "https://core.parts/os-95/programs/relate-/window-/title-bar/layout.css?constructor" => 'https://core.parts/os-95/programs/relate-/window-/title-bar/layout.css.c.js',
 "https://core.parts/os-95/programs/relate-/window-/title-bar/layout.css.c.js" => 'return `:host {
   background: ${(""+focus) === \'1\' ? \'rgb(0, 0, 163)\' : \'#7f7f7f\'};
   color: white;
   display: flex;
   flex-flow: row nowrap;
   align-items: center;
   gap: 3px;
   height: 18px;
   padding: 0px 2px;
   box-sizing: border-box;
  }
  app-icon {
   width: 16px;
   height: 16px;
  }`',
 "https://core.parts/os-95/programs/relate-/window-/title-bar/manifest.uri" => 'https://core.parts/os-95/programs/relate-/app-icon/ https://core.parts/os-95/programs/relate-/app-label/ https://core.parts/flex-spacer/ https://core.parts/os-95/programs/relate-/window-/window-controls/',
 "https://core.parts/os-95/programs/relate-/window-/title-bar/ondblclick.js?maximized" => 'https://core.parts/os-95/programs/relate-/window-/maximized.txt',
 "https://core.parts/os-95/programs/relate-/window-/title-bar/ondblclick.js?constructor" => 'https://core.parts/os-95/programs/relate-/window-/title-bar/ondblclick.c.js',
 "https://core.parts/os-95/programs/relate-/window-/title-bar/ondblclick.c.js" => 'return `() => { Ω[\'https://core.parts/os-95/programs/relate-/window-/${(""+maximized) === \'1\' ? \'restore\' : \'maximize\'}-button/onclick.js\']() }`',
 "https://core.parts/os-95/programs/relate-/window-/title-bar/?layout" => 'https://core.parts/os-95/programs/relate-/window-/title-bar/layout.css',
 "https://core.parts/os-95/programs/relate-/window-/title-bar/?manifest" => 'https://core.parts/os-95/programs/relate-/window-/title-bar/manifest.uri',
 "https://core.parts/os-95/programs/relate-/window-/title-bar/?ondblclick" => 'https://core.parts/os-95/programs/relate-/window-/title-bar/ondblclick.js',
 "https://core.parts/os-95/programs/relate-/window-/window-controls/layout.css" => '
  :host {
   display: flex;
   flex-flow: row nowrap
  }',
 "https://core.parts/os-95/programs/relate-/window-/window-controls/manifest.uri?maximized" => 'https://core.parts/os-95/programs/relate-/window-/maximized.txt',
 "https://core.parts/os-95/programs/relate-/window-/window-controls/manifest.uri?constructor" => 'https://core.parts/os-95/programs/relate-/window-/window-controls/manifest.uri.c.js',
 "https://core.parts/os-95/programs/relate-/window-/window-controls/manifest.uri.c.js" => 'return `https://core.parts/os-95/programs/relate-/window-/minimize-button/ https://core.parts/os-95/programs/relate-/window-/${(""+maximized) === \'1\' ? \'restore\' : \'maximize\'}-button/ https://core.parts/os-95/programs/relate-/window-/exit-button/`',
 "https://core.parts/os-95/programs/relate-/window-/window-controls/?layout" => 'https://core.parts/os-95/programs/relate-/window-/window-controls/layout.css',
 "https://core.parts/os-95/programs/relate-/window-/window-controls/?manifest" => 'https://core.parts/os-95/programs/relate-/window-/window-controls/manifest.uri',
 "https://core.parts/os-95/programs/relate-/window-/?layout" => 'https://core.parts/os-95/programs/relate-/window-/layout.css',
 "https://core.parts/os-95/programs/relate-/window-/?manifest" => 'https://core.parts/os-95/programs/relate-/window-/manifest.uri',
 "https://core.parts/os-95/programs/relate-/window-/?onfocus" => 'https://core.parts/os-95/programs/relate-/window-/onfocus.js',
 "https://core.parts/os-95/taskbar-/css-height.txt" => '28px',
 "https://core.parts/os-95/taskbar-/css-height/fx.uri" => 'https://core.parts/layout.css https://core.parts/os-95/taskbar-/start-menu/layout.css',
 "https://core.parts/os-95/taskbar-/layout.css" => '
  :host {
   position: relative;
   width: 100%;
   box-sizing: border-box;
   height: 100%;
   margin: 0;
   display: flex;
   flex-flow: row nowrap;
   gap: 3px;
   height: 100%;
   padding: 4px 2px 2px;
   background: #c3c3c3;
   box-shadow: inset 0 1px #c3c3c3, inset 0 2px white;
  }',
 "https://core.parts/os-95/taskbar-/manifest.uri?running_apps" => 'https://core.parts/os-95/tasks.uri',
 "https://core.parts/os-95/taskbar-/manifest.uri?constructor" => 'https://core.parts/os-95/taskbar-/manifest.uri.c.js',
 "https://core.parts/os-95/taskbar-/manifest.uri.c.js" => 'return `https://core.parts/os-95/taskbar-/start-button/ ${"" + running_apps ? running_apps + " " : ""}https://core.parts/flex-spacer/ https://core.parts/os-95/taskbar-/tray-/`',
 "https://core.parts/os-95/taskbar-/selected.txt" => '-1',
 "https://core.parts/os-95/taskbar-/selected.txt?fx" => 'https://core.parts/os-95/taskbar-/selected/fx.uri',
 "https://core.parts/os-95/taskbar-/selected.txt?constructor" => 'https://core.parts/os-95/taskbar-/selected.txt.c.js',
 "https://core.parts/os-95/taskbar-/selected.txt.c.js" => '
  let wasOn;
  const result = ""+(""+fx).split(" ").findIndex(x => {
   const
    src = caller,
    isX = x === src;
   wasOn = Δ[src] === "1";
   return (src && wasOn) ? isX : ("" + Ω[x] === "1");
  });
  return result;',
 "https://core.parts/os-95/taskbar-/selected/fx.uri" => 'https://core.parts/os-95/start-menu/open.txt https://core.parts/os-95/programs/locate-/window-/active.txt https://core.parts/os-95/programs/locate-/window-/onfocus.js https://core.parts/os-95/programs/relate-/window-/active.txt https://core.parts/os-95/desktop-/onfocus.js',
 "https://core.parts/os-95/taskbar-/start-button/icon-/layout.css?icon" => 'https://core.parts/apple-touch-icon.png',
 "https://core.parts/os-95/taskbar-/start-button/icon-/layout.css?constructor" => 'https://core.parts/os-95/taskbar-/start-button/icon-/layout.css.c.js',
 "https://core.parts/os-95/taskbar-/start-button/icon-/layout.css.c.js" => 'return `:host {
   position: relative;
   box-sizing: border-box;
   height: 100%;
   margin: 0;
   background: url(data:image/png;base64,${icon});
   background-size: 16px;
   width: 16px;
   height: 16px }`',
 "https://core.parts/os-95/taskbar-/start-button/icon-/?layout" => 'https://core.parts/os-95/taskbar-/start-button/icon-/layout.css',
 "https://core.parts/os-95/taskbar-/start-button/label-/layout.css" => '
  :host {
   position: relative;
   box-sizing: border-box;
   margin: 0;
   height: 16px }
  :host::before {
   content: "Start";
  }',
 "https://core.parts/os-95/taskbar-/start-button/label-/?layout" => 'https://core.parts/os-95/taskbar-/start-button/label-/layout.css',
 "https://core.parts/os-95/taskbar-/start-button/layout.css?open" => 'https://core.parts/os-95/start-menu/open.txt',
 "https://core.parts/os-95/taskbar-/start-button/layout.css?constructor" => 'https://core.parts/os-95/taskbar-/start-button/layout.css.c.js',
 "https://core.parts/os-95/taskbar-/start-button/layout.css.c.js" => '
  return `
   :host {
    flex: 0 0;
    position: relative;
    width: 100%;
    box-sizing: border-box;
    height: 100%;
    display: flex;
    flex-flow: row nowrap;
    gap: 3px;
    border: none;
    font: bold 11px / 16px sans-serif;
    box-sizing: border-box;
    padding: ${("" + open) === "0" ? 3 : 4}px 4px 2px;
    text-align: left;
    background: #c3c3c3;
    box-shadow: ${("" + open) === "0" ? `
     inset -1px -1px black,
     inset 1px 1px white,
     inset -2px -2px #7a7a7a,
     inset 2px 2px #dbdbdb`
     : `
     inset -1px -1px white,
     inset 1px 1px black,
     inset -2px -2px #dbdbdb,
     inset 2px 2px #7a7a7a`};
   }
   :host(:focus)::after {
    border: 1px dotted black;
    content: "";
    position: absolute;
    margin: 3px;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    pointer-events: none;
   }
  `',
 "https://core.parts/os-95/taskbar-/start-button/manifest.uri" => 'https://core.parts/os-95/taskbar-/start-button/icon-/ https://core.parts/os-95/taskbar-/start-button/label-/',
 "https://core.parts/os-95/taskbar-/start-button/manifest.uri?open" => 'https://core.parts/os-95/start-menu/open.txt',
 "https://core.parts/os-95/taskbar-/start-button/onclick.js" => '
  () => {
   Ω["https://core.parts/os-95/start-menu/open.txt"] = "1";
  }',
 "https://core.parts/os-95/taskbar-/start-button/?layout" => 'https://core.parts/os-95/taskbar-/start-button/layout.css',
 "https://core.parts/os-95/taskbar-/start-button/?manifest" => 'https://core.parts/os-95/taskbar-/start-button/manifest.uri',
 "https://core.parts/os-95/taskbar-/start-button/?onclick" => 'https://core.parts/os-95/taskbar-/start-button/onclick.js',
 "https://core.parts/os-95/taskbar-/start-menu/click-to-close/layout.css" => '
  :host {
   position: fixed;
   display: block;
   left: 0;
   top: 0;
   bottom: 0;
   right: 0;
   content: "";
  }',
 "https://core.parts/os-95/taskbar-/start-menu/click-to-close/onclick.js" => '
  () => {
   Ω["https://core.parts/os-95/start-menu/open.txt"] = "0";
  }',
 "https://core.parts/os-95/taskbar-/start-menu/click-to-close/?layout" => 'https://core.parts/os-95/taskbar-/start-menu/click-to-close/layout.css',
 "https://core.parts/os-95/taskbar-/start-menu/click-to-close/?onclick" => 'https://core.parts/os-95/taskbar-/start-menu/click-to-close/onclick.js',
 "https://core.parts/os-95/taskbar-/start-menu/layout.css?height" => 'https://core.parts/os-95/taskbar-/css-height.txt',
 "https://core.parts/os-95/taskbar-/start-menu/layout.css?constructor" => 'https://core.parts/os-95/taskbar-/start-menu/layout.css.c.js',
 "https://core.parts/os-95/taskbar-/start-menu/layout.css.c.js" => 'return `:host {
   position: relative;
   min-width: 164px;
   display: flex;
   flex-flow: column nowrap;
   position: absolute;
   left: 2px;
   bottom: calc(${height}
  - 4px);
   user-select: none;
   line-height: 18px;
   text-align: left;
   background: #c3c3c3;
   box-sizing: border-box;
   padding: 3px 3px 3px 24px;
   text-align: left;
   background: #c3c3c3;
   box-shadow: inset -1px -1px black, inset 1px 1px white, inset -2px -2px #7a7a7a, inset 2px 2px #dbdbdb }
  :host::after {
   pointer-events: none;
   display: block;
   content: "";
   position: absolute;
   left: 3px;
   top: 3px;
   bottom: 3px;
   background: #7f7f7f;
   width: 21px }`',
 "https://core.parts/os-95/taskbar-/start-menu/manifest.uri" => 'https://core.parts/os-95/programs/locate-/start-menu-item/ https://core.parts/os-95/programs/relate-/start-menu-item/ https://core.parts/os-95/taskbar-/start-menu/network-folder/ https://core.parts/os-95/horizontal-line/ https://core.parts/os-95/taskbar-/start-menu/save-computer/ https://core.parts/os-95/taskbar-/start-menu/restart-computer/ https://core.parts/os-95/taskbar-/start-menu/restart-server/',
 "https://core.parts/os-95/taskbar-/start-menu/network-folder/app-icon/layout.css" => '
  :host {
   --size: 16px;
  }
  :host::before {
   content: "🔭";
   font-size: var(--size);
   line-height: var(--size);
  }',
 "https://core.parts/os-95/taskbar-/start-menu/network-folder/app-icon/?layout" => 'https://core.parts/os-95/taskbar-/start-menu/network-folder/app-icon/layout.css',
 "https://core.parts/os-95/taskbar-/start-menu/network-folder/app-label/layout.css" => ':host::after {
   height: 24px;
   content: "Network";
  }',
 "https://core.parts/os-95/taskbar-/start-menu/network-folder/app-label/?layout" => 'https://core.parts/os-95/taskbar-/start-menu/network-folder/app-label/layout.css',
 "https://core.parts/os-95/taskbar-/start-menu/network-folder/layout.css" => '
  :host {
   position: relative;
   display: flex;
   flex-flow: row nowrap;
   align-items: center;
   padding: 4px 0 }
  :host(:hover) {
   background: #00007f;
   color: white }
  app-icon {
   width: 24px;
   height: 24px;
   margin: 0 10px;
   --size: 24px;
  }',
 "https://core.parts/os-95/taskbar-/start-menu/network-folder/manifest.uri" => 'https://core.parts/os-95/taskbar-/start-menu/network-folder/app-icon/ https://core.parts/os-95/taskbar-/start-menu/network-folder/app-label/',
 "https://core.parts/os-95/taskbar-/start-menu/network-folder/?layout" => 'https://core.parts/os-95/taskbar-/start-menu/network-folder/layout.css',
 "https://core.parts/os-95/taskbar-/start-menu/network-folder/?manifest" => 'https://core.parts/os-95/taskbar-/start-menu/network-folder/manifest.uri',
 "https://core.parts/os-95/taskbar-/start-menu/restart-computer/app-icon/layout.css" => '
  :host {
   --size: 16px;
   width: var(--size);
   height: var(--size);
   font-size: var(--size);
   line-height: var(--size);
  }
  :host::after {
   content: "🖥";
  }',
 "https://core.parts/os-95/taskbar-/start-menu/restart-computer/app-icon/?layout" => 'https://core.parts/os-95/taskbar-/start-menu/restart-computer/app-icon/layout.css',
 "https://core.parts/os-95/taskbar-/start-menu/restart-computer/app-label/layout.css" => ':host::after {
   height: 24px;
   content: "Load Last Save";
  }',
 "https://core.parts/os-95/taskbar-/start-menu/restart-computer/app-label/?layout" => 'https://core.parts/os-95/taskbar-/start-menu/restart-computer/app-label/layout.css',
 "https://core.parts/os-95/taskbar-/start-menu/restart-computer/layout.css" => '
  :host {
   position: relative;
   display: flex;
   flex-flow: row nowrap;
   align-items: center;
   padding: 4px 0;
   padding-right: 6px }
  :host(:hover) {
   background: #00007f;
   color: white }
  app-icon {
   width: 24px;
   height: 24px;
   margin: 0 10px;
   --size: 24px;
  }',
 "https://core.parts/os-95/taskbar-/start-menu/restart-computer/manifest.uri" => 'https://core.parts/os-95/taskbar-/start-menu/restart-computer/app-icon/ https://core.parts/os-95/taskbar-/start-menu/restart-computer/app-label/',
 "https://core.parts/os-95/taskbar-/start-menu/restart-computer/onclick.js" => '
  () => {
   location.reload();
  }',
 "https://core.parts/os-95/taskbar-/start-menu/restart-computer/?layout" => 'https://core.parts/os-95/taskbar-/start-menu/restart-computer/layout.css',
 "https://core.parts/os-95/taskbar-/start-menu/restart-computer/?manifest" => 'https://core.parts/os-95/taskbar-/start-menu/restart-computer/manifest.uri',
 "https://core.parts/os-95/taskbar-/start-menu/restart-computer/?onclick" => 'https://core.parts/os-95/taskbar-/start-menu/restart-computer/onclick.js',
 "https://core.parts/os-95/taskbar-/start-menu/restart-server/app-icon/layout.css" => '
  :host {
   --size: 16px;
   width: var(--size);
   height: var(--size);
   font-size: var(--size);
   line-height: var(--size);
  }
  :host::after {
   content: "🧼";
  }',
 "https://core.parts/os-95/taskbar-/start-menu/restart-server/app-icon/?layout" => 'https://core.parts/os-95/taskbar-/start-menu/restart-server/app-icon/layout.css',
 "https://core.parts/os-95/taskbar-/start-menu/restart-server/app-label/layout.css" => ':host::after {
   height: 24px;
   content: "Factory Reset";
  }',
 "https://core.parts/os-95/taskbar-/start-menu/restart-server/app-label/?layout" => 'https://core.parts/os-95/taskbar-/start-menu/restart-server/app-label/layout.css',
 "https://core.parts/os-95/taskbar-/start-menu/restart-server/layout.css" => '
  :host {
   position: relative;
   display: flex;
   flex-flow: row nowrap;
   align-items: center;
   padding: 4px 0 }
  :host(:hover) {
   background: #00007f;
   color: white }
  app-icon {
   width: 24px;
   height: 24px;
   margin: 0 10px;
   --size: 24px;
  }',
 "https://core.parts/os-95/taskbar-/start-menu/restart-server/manifest.uri" => 'https://core.parts/os-95/taskbar-/start-menu/restart-server/app-icon/ https://core.parts/os-95/taskbar-/start-menu/restart-server/app-label/',
 "https://core.parts/os-95/taskbar-/start-menu/restart-server/onclick.js" => '
  () => {
   navigator.serviceWorker.controller.postMessage("restart");
   setTimeout(() => location.reload(), 1000);
  }',
 "https://core.parts/os-95/taskbar-/start-menu/restart-server/?layout" => 'https://core.parts/os-95/taskbar-/start-menu/restart-server/layout.css',
 "https://core.parts/os-95/taskbar-/start-menu/restart-server/?manifest" => 'https://core.parts/os-95/taskbar-/start-menu/restart-server/manifest.uri',
 "https://core.parts/os-95/taskbar-/start-menu/restart-server/?onclick" => 'https://core.parts/os-95/taskbar-/start-menu/restart-server/onclick.js',
 /*
  "https://core.parts/os-95/taskbar-/start-menu/save-computer-as/app-icon/?layout" => 'https://core.parts/os-95/taskbar-/start-menu/save-computer/app-icon/layout.css',
  "https://core.parts/os-95/taskbar-/start-menu/save-computer-as/app-label/layout.css" => ':host::after {
    height: 24px;
    content: "Save as ServiceWorker source";
   }',
  "https://core.parts/os-95/taskbar-/start-menu/save-computer-as/app-label/?layout" => 'https://core.parts/os-95/taskbar-/start-menu/save-computer-as/app-label/layout.css',
  "https://core.parts/os-95/taskbar-/start-menu/save-computer-as/layout.css" => '
   :host {
    position: relative;
    display: flex;
    flex-flow: row nowrap;
    align-items: center;
    padding: 4px 0;
    padding-right: 6px }
   :host(:hover) {
    background: #00007f;
    color: white }
   app-icon {
    width: 24px;
    height: 24px;
    margin: 0 10px;
    --size: 24px;
   }',
  "https://core.parts/os-95/taskbar-/start-menu/save-computer-as/manifest.uri" => 'https://core.parts/os-95/taskbar-/start-menu/save-computer-as/app-icon/ https://core.parts/os-95/taskbar-/start-menu/save-computer-as/app-label/',
  "https://core.parts/os-95/taskbar-/start-menu/save-computer-as/onclick.js" => '
   () => {
    Ω["https://core.parts/os-95/start-menu/open.txt"] = "0"
    delete Δ["https://core.parts/os-95/taskbar-/tray-/clock-/date.txt"]
    delete Δ["https://core.parts/os-95/taskbar-/tray-/clock-/layout.css"]
    const
     a = document.createElement("a"),
     json = JSON.stringify(Object.keys(Δ).sort().reduce((temp_obj, key) => { temp_obj[key] = Δ[key]; return temp_obj }, {})).replace(/","/g,"\",\n  \"").replace(/^{/s, "{\n  ").replace(/}$/s, "\n}"),
     js = `var causality={},onfetch=(Ω=new Proxy({},new Proxy(${json},{get:(Δ,Υ)=>eval(Δ[V="https://core.parts/proxy/alpha.js"])})))["https://core.parts/file.js"];onmessage=Ω["https://core.parts/client-to-server.js"]`,
     ourl = URL.createObjectURL(new Blob([js], { type: "text/javascript" }));
     a.href = ourl
     a.download = "everything.js"
     document.body.appendChild(a)
    a.click();
     a.remove()
     URL.revokeObjectURL(ourl);
   }',
  "https://core.parts/os-95/taskbar-/start-menu/save-computer-as/?layout" => 'https://core.parts/os-95/taskbar-/start-menu/save-computer-as/layout.css',
  "https://core.parts/os-95/taskbar-/start-menu/save-computer-as/?manifest" => 'https://core.parts/os-95/taskbar-/start-menu/save-computer-as/manifest.uri',
  "https://core.parts/os-95/taskbar-/start-menu/save-computer-as/?onclick" => 'https://core.parts/os-95/taskbar-/start-menu/save-computer-as/onclick.js',
 */
 "https://core.parts/os-95/taskbar-/start-menu/save-computer/app-icon/layout.css" => '
  :host {
   --size: 16px;
   width: var(--size);
   height: var(--size);
   font-size: var(--size);
   line-height: var(--size);
  }
  :host::after {
   content: "💽";
  }',
 "https://core.parts/os-95/taskbar-/start-menu/save-computer/app-icon/?layout" => 'https://core.parts/os-95/taskbar-/start-menu/save-computer/app-icon/layout.css',
 "https://core.parts/os-95/taskbar-/start-menu/save-computer/app-label/layout.css" => ':host::after {
   height: 24px;
   content: "Quick Save";
  }',
 "https://core.parts/os-95/taskbar-/start-menu/save-computer/app-label/?layout" => 'https://core.parts/os-95/taskbar-/start-menu/save-computer/app-label/layout.css',
 "https://core.parts/os-95/taskbar-/start-menu/save-computer/layout.css" => '
  :host {
   position: relative;
   display: flex;
   flex-flow: row nowrap;
   align-items: center;
   padding: 4px 0;
   padding-right: 6px }
  :host(:hover) {
   background: #00007f;
   color: white }
  app-icon {
   width: 24px;
   height: 24px;
   margin: 0 10px;
   --size: 24px;
  }',
 "https://core.parts/os-95/taskbar-/start-menu/save-computer/manifest.uri" => 'https://core.parts/os-95/taskbar-/start-menu/save-computer/app-icon/ https://core.parts/os-95/taskbar-/start-menu/save-computer/app-label/',
 "https://core.parts/os-95/taskbar-/start-menu/save-computer/onclick.js" => '
  () => {
   Ω["https://core.parts/os-95/start-menu/open.txt"] = "0"
   delete Δ["https://core.parts/os-95/taskbar-/tray-/clock-/date.txt"]
   delete Δ["https://core.parts/os-95/taskbar-/tray-/clock-/layout.css"]
   navigator.serviceWorker.controller.postMessage(Δ);
  }',
 "https://core.parts/os-95/taskbar-/start-menu/save-computer/?layout" => 'https://core.parts/os-95/taskbar-/start-menu/save-computer/layout.css',
 "https://core.parts/os-95/taskbar-/start-menu/save-computer/?manifest" => 'https://core.parts/os-95/taskbar-/start-menu/save-computer/manifest.uri',
 "https://core.parts/os-95/taskbar-/start-menu/save-computer/?onclick" => 'https://core.parts/os-95/taskbar-/start-menu/save-computer/onclick.js',
 "https://core.parts/os-95/taskbar-/start-menu/?layout" => 'https://core.parts/os-95/taskbar-/start-menu/layout.css',
 "https://core.parts/os-95/taskbar-/start-menu/?manifest" => 'https://core.parts/os-95/taskbar-/start-menu/manifest.uri',
 "https://core.parts/os-95/taskbar-/tray-/clock-/date.txt?fx" => 'https://core.parts/os-95/taskbar-/tray-/clock-/date/fx.uri',
 "https://core.parts/os-95/taskbar-/tray-/clock-/date.txt?constructor" => 'https://core.parts/os-95/taskbar-/tray-/clock-/date.txt.c.js',
 "https://core.parts/os-95/taskbar-/tray-/clock-/date.txt.c.js" => 'return new Date().toLocaleString("en-US", { hour: "numeric", minute: "numeric", hourCycle: "h12" })',
 "https://core.parts/os-95/taskbar-/tray-/clock-/date/fx.uri" => 'https://core.parts/os-95/taskbar-/tray-/clock-/layout.css',
 "https://core.parts/os-95/taskbar-/tray-/clock-/layout.css?date" => 'https://core.parts/os-95/taskbar-/tray-/clock-/date.txt',
 "https://core.parts/os-95/taskbar-/tray-/clock-/layout.css?constructor" => 'https://core.parts/os-95/taskbar-/tray-/clock-/layout.css.c.js',
 "https://core.parts/os-95/taskbar-/tray-/clock-/layout.css.c.js" => '
  const minute = 1000 * 60, delay = minute - (Date.now() % minute);
  setTimeout(()=>{
   Ω[date.headerOf().href] = new Date().toLocaleString("en-US", {
    hour: "numeric",
    minute: "numeric",
    hourCycle: "h12"
   })
  }, delay + 5);
  return `:host::after {
   content: "${date}";
   white-space: nowrap;
  }`',
 "https://core.parts/os-95/taskbar-/tray-/clock-/?layout" => 'https://core.parts/os-95/taskbar-/tray-/clock-/layout.css',
 "https://core.parts/os-95/taskbar-/tray-/layout.css" => ':host {
   position: relative;
   display: flex;
   flex-flow: row nowrap;
   gap: 3px;
   box-sizing: border-box;
   height: 100%;
   margin: 0;
   user-select: none;
   padding: 3px 4px 3px;
   text-align: left;
   background: #c3c3c3;
   box-shadow: inset -1px -1px white, inset 1px 1px #7a7a7a;
  }',
 "https://core.parts/os-95/taskbar-/tray-/manifest.uri" => 'https://core.parts/os-95/taskbar-/tray-/factory-reset/ https://core.parts/os-95/taskbar-/tray-/fullscreen-/ https://core.parts/os-95/taskbar-/tray-/clock-/',
 "https://core.parts/os-95/taskbar-/tray-/?layout" => 'https://core.parts/os-95/taskbar-/tray-/layout.css',
 "https://core.parts/os-95/taskbar-/tray-/?manifest" => 'https://core.parts/os-95/taskbar-/tray-/manifest.uri',
 "https://core.parts/os-95/taskbar-/?layout" => 'https://core.parts/os-95/taskbar-/layout.css',
 "https://core.parts/os-95/taskbar-/?manifest" => 'https://core.parts/os-95/taskbar-/manifest.uri',
 "https://core.parts/os-95/tasks.uri" => 'https://core.parts/os-95/programs/locate-/task-/ https://core.parts/os-95/programs/relate-/task-/',
 "https://core.parts/os-95/windows.uri" => 'https://core.parts/os-95/programs/locate-/window-/ https://core.parts/os-95/programs/relate-/window-/',
 "https://core.parts/os-95/windows.uri?fx" => 'https://core.parts/os-95/windows-fx.uri',
 "https://core.parts/os-95/windows-fx.uri" => 'https://core.parts/os-95/manifest.uri',
 "https://core.parts/os-95/icons/application-json-icon/layout.css?layout" => 'https://core.parts/os-95/icons/layout.js',
 "https://core.parts/os-95/icons/application-json-icon/layout.css?constructor" => 'https://core.parts/os-95/icons/application-json-icon/layout.css.c.js',
 "https://core.parts/os-95/icons/application-json-icon/layout.css.c.js" => 'return layout([220, 220, 255], \'\u{1F4C4}\', \'json\', [1/7, 1/16, 1/8])',
 "https://core.parts/os-95/icons/application-json-icon/?layout" => 'https://core.parts/os-95/icons/application-json-icon/layout.css',
 "https://core.parts/os-95/icons/application-wasm-icon/layout.css?layout" => 'https://core.parts/os-95/icons/layout.js',
 "https://core.parts/os-95/icons/application-wasm-icon/layout.css?constructor" => 'https://core.parts/os-95/icons/application-wasm-icon/layout.css.c.js',
 "https://core.parts/os-95/icons/application-wasm-icon/layout.css.c.js" => 'return layout([0, 0, 0, 0], \'\u{1F4E6}\')',
 "https://core.parts/os-95/icons/application-wasm-icon/?layout" => 'https://core.parts/os-95/icons/application-wasm-icon/layout.css',
 "https://core.parts/os-95/icons/folder-icon/layout.css" => '
  :host {
   --size: 16px;
   width: var(--size);
   height: var(--size);
   font-size: var(--size);
   line-height: var(--size);
  }
  :host::before {
   content: \'📁\';
  }',
 "https://core.parts/os-95/icons/folder-icon/?layout" => 'https://core.parts/os-95/icons/folder-icon/layout.css',
 "https://core.parts/os-95/icons/protocol-icon/layout.css" => '
  :host {
   --size: 16px;
   width: var(--size);
   height: var(--size);
   font-size: var(--size);
   line-height: var(--size);
  }
  :host::before {
   content: \'⭄\';
  }',
 "https://core.parts/os-95/icons/protocol-icon/?layout" => 'https://core.parts/os-95/icons/protocol-icon/layout.css',
 "https://core.parts/os-95/icons/image-png-icon/layout.css?layout" => 'https://core.parts/os-95/icons/layout.js',
 "https://core.parts/os-95/icons/image-png-icon/layout.css?constructor" => 'https://core.parts/os-95/icons/image-png-icon/layout.css.c.js',
 "https://core.parts/os-95/icons/image-png-icon/layout.css.c.js" => 'return layout([255, 127, 0], \'\u{1F4C4}\', \'png\')',
 "https://core.parts/os-95/icons/image-png-icon/?layout" => 'https://core.parts/os-95/icons/image-png-icon/layout.css',
 "https://core.parts/os-95/icons/image-vnd-microsoft-icon-icon/layout.css?layout" => 'https://core.parts/os-95/icons/layout.js',
 "https://core.parts/os-95/icons/image-vnd-microsoft-icon-icon/layout.css?constructor" => 'https://core.parts/os-95/icons/image-vnd-microsoft-icon-icon/layout.css.c.js',
 "https://core.parts/os-95/icons/image-vnd-microsoft-icon-icon/layout.css.c.js" => 'return layout([127, 127, 127, 0.25], \'\u{1F4C4}\', \'ico\', [0.1, 0.1, 0.1])',
 "https://core.parts/os-95/icons/image-vnd-microsoft-icon-icon/?layout" => 'https://core.parts/os-95/icons/image-vnd-microsoft-icon-icon/layout.css',
 "https://core.parts/os-95/icons/kireji-icon/layout.css" => '
  :host {
   --size: 16px;
   width: var(--size);
   height: var(--size);
   font-size: var(--size);
   line-height: var(--size);
  }
  :host::before {
   content: \'🔗\';
  }',
 "https://core.parts/os-95/icons/kireji-icon/?layout" => 'https://core.parts/os-95/icons/kireji-icon/layout.css',
 "https://core.parts/os-95/icons/layout.js" => '
  ([bgr, bgg, bgb, bga = 0.8], c, ext, [r = 0, g = 0, b = 0, a = 1] = []) => {
   return `
    :host {
     --rgb-bg: rgba(${bgr}, ${bgg}, ${bgb}, ${bga});
     --rgb: ${r}, ${g}, ${b};
     --character: \'${c}\';
     --size: 16px;
     --unit: calc(var(--size) / 16);
     color: rgba(var(--rgb), ${a});
     position: relative;
     width: var(--size);
     height: var(--size);
    }
    :host::before,
    :host::after {
     border-radius: calc(var(--size) / 6);
    }
    :host::before {
     content: var(--character);
     font-size: var(--size);
     line-height: var(--size);
    }
    :host::after {
     box-shadow: 0 0 0 var(--unit) rgba(var(--rgb), ${a/2});
     background: var(--rgb-bg);
     position: absolute;
     bottom: var(--unit);
     right: 0;${ext ? `
     content: \'${ext}\';` : ``}
     font: 400 calc(var(--size) / 3) / calc(var(--size) / 3) monospace;
     padding: var(--unit);
    }
   `
  }',
 "https://core.parts/os-95/icons/domain-icon/layout.css" => '
  :host {
   --size: 16px;
   width: var(--size);
   height: var(--size);
   font-size: var(--size);
   line-height: var(--size);
  }
  :host::before {
   content: \'🗄\';
  }',
 "https://core.parts/os-95/icons/domain-icon/?layout" => 'https://core.parts/os-95/icons/domain-icon/layout.css',
 "https://core.parts/os-95/icons/text-css-icon/layout.css?layout" => 'https://core.parts/os-95/icons/layout.js',
 "https://core.parts/os-95/icons/text-css-icon/layout.css?constructor" => 'https://core.parts/os-95/icons/text-css-icon/layout.css.c.js',
 "https://core.parts/os-95/icons/text-css-icon/layout.css.c.js" => 'return layout([0, 255, 255], \'\u{1F4C4}\', \'css\')',
 "https://core.parts/os-95/icons/text-css-icon/?layout" => 'https://core.parts/os-95/icons/text-css-icon/layout.css',
 "https://core.parts/os-95/icons/text-html-icon/layout.css?layout" => 'https://core.parts/os-95/icons/layout.js',
 "https://core.parts/os-95/icons/text-html-icon/layout.css?constructor" => 'https://core.parts/os-95/icons/text-html-icon/layout.css.c.js',
 "https://core.parts/os-95/icons/text-html-icon/layout.css.c.js" => 'return layout([255, 255, 255], \'\u{1F4C4}\', \'html\')',
 "https://core.parts/os-95/icons/text-html-icon/?layout" => 'https://core.parts/os-95/icons/text-html-icon/layout.css',
 "https://core.parts/os-95/icons/text-javascript-icon/layout.css?layout" => 'https://core.parts/os-95/icons/layout.js',
 "https://core.parts/os-95/icons/text-javascript-icon/layout.css?constructor" => 'https://core.parts/os-95/icons/text-javascript-icon/layout.css.c.js',
 "https://core.parts/os-95/icons/text-javascript-icon/layout.css.c.js" => 'return layout([255, 127, 127, 0.7], \'\u{1F4C4}\', \'js\', [0.4])',
 "https://core.parts/os-95/icons/text-javascript-icon/?layout" => 'https://core.parts/os-95/icons/text-javascript-icon/layout.css',
 "https://core.parts/os-95/icons/text-plain-icon/layout.css" => '
  :host {
   --size: 16px;
   width: var(--size);
   height: var(--size) }
  :host::before {
   content: \'📄\';
   font-size: var(--size);
   line-height: var(--size)
  }',
 "https://core.parts/os-95/icons/text-plain-icon/?layout" => 'https://core.parts/os-95/icons/text-plain-icon/layout.css',
 "https://core.parts/os-95/icons/text-uri-list-icon/layout.css" => '
  :host {
   --rgba: rgba(0, 0, 0, 0.8);
   --character: \'📄\';
   --size: 16px;
   color: #ffff3f;
   position: relative;
   width: 16px;
   height: 16px;
  }
  :host::before,
  :host::after {
   border-radius: calc(var(--size) / 6);
  }
  :host::before {
   content: var(--character);
   font-size: var(--size);
   line-height: var(--size);
  }
  :host::after {
   box-shadow: 0 0 0 calc(var(--size) / 16) #ffff3f;
   background: var(--rgba);
   position: absolute;
   bottom: 0;
   right: 0;
   content: \'uri\';
   font: 400 calc(var(--size) / 3) / calc(var(--size) / 3) monospace;
   padding: calc(var(--size) / 16);
  }',
 "https://core.parts/os-95/icons/text-uri-list-icon/layout.cs?layout" => 'https://core.parts/os-95/icons/layout.js',
 "https://core.parts/os-95/icons/text-uri-list-icon/layout.css?constructor" => 'https://core.parts/os-95/icons/text-uri-list-icon/layout.css.c.js',
 "https://core.parts/os-95/icons/text-uri-list-icon/layout.css.c.js" => 'return layout([0, 0, 0], "\u{1F4C4}", "uri", [1, 1, 0.3])',
 "https://core.parts/os-95/icons/text-uri-list-icon/?layout" => 'https://core.parts/os-95/icons/text-uri-list-icon/layout.css',
 "https://core.parts/os-95/letters/capital-f/layout.css" => '
  :host::before {
   content: "F"
  }',
 "https://core.parts/os-95/letters/capital-f/?layout" => 'https://core.parts/os-95/letters/capital-f/layout.css',
 "https://core.parts/os-95/letters/lowercase-e/layout.css" => ':host::before {
   content: "e"
  }',
 "https://core.parts/os-95/letters/lowercase-e/?layout" => 'https://core.parts/os-95/letters/lowercase-e/layout.css',
 "https://core.parts/os-95/letters/lowercase-i/layout.css" => ':host::before {
   content: "i"
  }',
 "https://core.parts/os-95/letters/lowercase-i/?layout" => 'https://core.parts/os-95/letters/lowercase-i/layout.css',
 "https://core.parts/os-95/letters/lowercase-l/layout.css" => ':host::before {
   content: "l"
  }',
 "https://core.parts/os-95/letters/lowercase-l/?layout" => 'https://core.parts/os-95/letters/lowercase-l/layout.css',
 "https://core.parts/os-95/start-menu/open-fx.uri" => 'https://core.parts/os-95/taskbar-/start-button/layout.css https://core.parts/os-95/taskbar-/selected.txt https://core.parts/os-95/manifest.uri',
 "https://core.parts/os-95/start-menu/open.txt" => '0',
 "https://core.parts/os-95/start-menu/open.txt?fx" => 'https://core.parts/os-95/start-menu/open-fx.uri',
 "https://core.parts/os-95/start-menu/open.txt?selected" => 'https://core.parts/os-95/taskbar-/selected.txt',
 "https://core.parts/os-95/start-menu/open.txt?constructor" => 'https://core.parts/os-95/start-menu/open.txt.c.js',
 "https://core.parts/os-95/start-menu/open.txt.c.js" => 'return ("" + selected) ==="0" ? "1" : "0"',
 "https://core.parts/proxy/alpha.js" => '
  ({
   get:
    (_, υ) => {
     const
      regex = /^(?<protocol>[a-z+]+:\/\/?)(?:(?<host>[^\/]+?)(?:\/(?<path>(?:[^\s.?\/]+?\/)*)(?:(?<part>[a-z][a-z0-9]*-[a-z0-9-]*)\/?|(?<filename>[^\s?\/]*)\.(?<extension>(?<binary>png|ico|woff2|wasm)|[^\s.?\/]+))|\/(?<index>(?:[^\s.?\/]+?\/)*))(?:\?(?<kireji>[a-zA-Z][a-zA-Z0-9_]*)(?:=(?<value>-?[\d]*\.?[\d]*)(?<rest_kireji>&(?:[a-zA-Z][a-zA-Z0-9_]*=-?[\d]*\.?[\d]*)+)?$)?)?)?$/,
      Ψ = υ.match(regex)?.groups;
     if (!Ψ) {
      throw new TypeError(\'bad request: \' + υ)
     }
     const
      extras = {
       size: {
        get() {
         return Δ[υ]?.length ?? 0
        }
       },
       entrySize: {
        get() {
         return this.size + υ.length
        }
       }
      },
      types = {
       js: "text/javascript",
       css: "text/css",
       json: "application/json",
       png: "image/png",
       woff2: "font/woff2",
       ico: "image/vnd.microsoft.icon",
       html: "text/html",
       wasm: "application/wasm",
       uri: "text/uri-list"
      },
      true_extension = Ψ.value ? "js"
                       : (Ψ.index !== undefined || Ψ.part !== undefined) ? "html"
                       : (Ψ.kireji === undefined) ? Ψ.extension
                       : "uri";
     Object.defineProperties(Ψ, extras)
     if (Ψ.value)
      Ψ.target = υ.slice(0, - Ψ.kireji.length - (2 + Ψ.value.length))
     Ψ.type = types[true_extension] ?? "text/plain";
     let α, β;
     α = new Proxy(Proxy, {
      get: (_, π) => {
       if (π === Symbol.toPrimitive) π = \'toPrimitive\';
       const result = eval(`(${Δ[Δ[`${υ}?${π}`] ?? Δ[`${Δ[`${υ}?core`] ?? \'https://core.parts/core-part/\'}?${π}`]] ?? Δ[`https://core.parts/proxy/beta/${π}.js`]})`)
       return result
      }
     })
     return β = new Proxy(α, α)
    },
   set:
    (_, υ, δ) => {
     if (Δ[υ] === δ)
      return
     const
      payload = { [υ]: δ },
      onset = data => {
       for (const url in data)
        if (url in causality)
         Object.entries(causality[url]).forEach(
          ([kireji, nodeset]) => {
           nodeset.forEach(node => node[kireji] = data[url])
          }
         )
      }
     Δ[υ] = δ
     if (globalThis.coresetlock) return onset(payload)
     globalThis.coresetlock = true
     const
      fxdom = {},
      fxneg = {},
      fxall = new Set(),
      recursive_getfx = (cause, affected, level) => {
       fxneg[cause] = affected
       for (const url of affected) {
        if (!(url in fxdom)) {
         fxdom[url] = new Set()
         if (cause) fxdom[url].add(level + \'|\' + cause)
         if (url === \'undefined\') continue;
         fxall.add(url)
         recursive_getfx(url, ("" + Ω[url].fx).split(\' \'), level + 1)
        } else {
         fxdom[url].add(level + \'|\' + cause)
        }
       }
      }
     recursive_getfx(undefined, [υ], 0)
     const
      seen = new Set(),
      order = [...fxall],
      extract = item => {
       if (!order.includes(item)) return;
       order.splice(order.indexOf(item), 1)
      },
      moveToStart = item => {
       if (!order.includes(item)) return;
       order.unshift(extract(item)[0])
      },
      recursive_getprio = item => {
       if (seen.has(item) || !order.includes(item)) return;
       fxdom[item].forEach(moveToStart)
       fxdom[item].forEach(recursive_getprio)
      }
     extract(υ)
     extract("undefined")
     recursive_getprio(υ)
     // TODO: Allow a script to set others?
     order.forEach(url => {
      const
       existing = Δ[url],
       generated = Ω[url].toPrimitive("imagine", υ);
      if (existing !== generated) {
       payload[url] = Δ[url] = generated
       // TODO: verify. For all fx of current url whose own url already passed through this callback,
       // imagine the fx\'s value again. maybe it changed? That would be a consistency issue.
      }
     })
     onset(payload)
     globalThis.coresetlock = false
    }
  }[Υ])',
 "https://core.parts/proxy/beta/apply.js" => '
  (_, __, A) => {
   return eval("" + α)(...A)
  }',
 "https://core.parts/proxy/beta/get.js" => '
  (_, π) => {
   // console.groupCollapsed("get", { υ, π })
   // console.trace()
   if ([\'toPrimitive\', Symbol.toPrimitive, \'toString\', \'valueOf\', \'headerOf\', \'rootsOf\', \'query\'].includes(π)) {
    // console.log("Fetching it from alpha proxy.")
    // console.groupEnd()
    return α[π]
   }
   let p, r = p = υ, url, result, exists, core_url = "https://core.parts/core-part/"
   do {
    exists = (result = Δ[url = `${r}?${π}`]) !== undefined
    // console.log(`${url} does${exists ? "" : "n\'t"} exist`)
    if (exists) {
     console.groupEnd()
     return Ω[result]
    }
    if (r === core_url) break
    p = r
    r = Δ[`${r}?core`] ?? core_url
    // console.log(`${p} takes Δ[${p}?core] to become ${r}`)
   } while (r !== p)
   // console.log(`${π} isn\'t in ${υ}`)
   // console.groupEnd()
  }',
 "https://core.parts/proxy/beta/getOwnPropertyDescriptor.js" => '
  (_, π) => ({
   configurable: true,
   enumerable: true,
   writable: true,
   value: α
  })',
 "https://core.parts/proxy/beta/getPrototypeOf.js" => '
  () => {
   return Object.prototype
  }',
 "https://core.parts/proxy/beta/has.js" => '
  (_, π) => {
   // console.groupCollapsed("has", { υ, π })
   // console.trace()
   if ([\'toPrimitive\', Symbol.toPrimitive, \'toString\', \'valueOf\', \'headerOf\', \'rootsOf\', \'query\'].includes(π)) {
    // console.log("Fetching it from alpha proxy.")
    // console.groupEnd()
    return α[π] !== undefined
   }
   let p, r = p = υ, url, result, exists, core_url = "https://core.parts/core-part/"
   do {
    exists = (result = Δ[url = `${r}?${π}`]) !== undefined
    // console.log(`${url} does${exists ? "" : "n\'t"} exist`)
    if (exists) {
     console.groupEnd()
     return true
    }
    if (r === core_url) break
    p = r
    r = Δ[`${r}?core`] ?? core_url
    // console.log(`${p} takes Δ[${p}?core] to become ${r}`)
   } while (r !== p)
   // console.log(`${π} isn\'t in ${υ}`)
   // console.groupEnd()
  }',
 "https://core.parts/proxy/beta/headerOf.js" => '
  () => ({
   kernelActionLocation: V,
   kernelActionKey: Υ,
   href: υ,
   metaKernel: α,
   self: β,
   groups: Ψ,
   metaKernelKey: π
  })',
 "https://core.parts/proxy/beta/isExtensible.js" => '
  () => {
   return true
  }',
 "https://core.parts/proxy/beta/ownKeys.js" => '
  () => {
   //console.groupCollapsed("keys", { υ })
   //console.trace()
   const core_url = "https://core.parts/core-part/", keys = new Set()
   for (const url in Δ) {
    if (!url.match(/^[^?]*\?\w*$/)) continue
    let p, r = p = υ
    const [base, π] = url.split("?")
    if (keys.has(π)) continue;
    do {
     if (r === base) { keys.add(π); break }
     if (r === core_url) break;
     p = r
     r = Δ[`${r}?core`] ?? core_url
    } while (r !== p)
   }
   const result = [...keys]
   //console.log(result)
   //console.groupEnd()
   return result;
  }',
 "https://core.parts/proxy/beta/query.js" => '
  (ƒ = x => x) => {
   const roots = β.rootsOf()
   return Object.keys(Δ).reduce((o, url) => {
    const rootIndex = roots.findIndex(root => url.startsWith(root + \'?\'));
    if (rootIndex !== -1) {
     const root = roots[rootIndex],
      kireji = url.slice(root.length + 1)
     const item = { url, root, kireji, rootIndex, href: Δ[url] }
     const result = ƒ(item)
     if (result) o.push(result)
    }
    return o
   }, [])
  }',
 "https://core.parts/proxy/beta/rootsOf.js" => '
  () => {
   const roots = [υ], protocore = "https://core.parts/core-part/"
   if (υ === protocore) throw "recursion"
   let root = υ, key;
   while (root = Δ[key = root + \'?core\']) {
    if (roots.includes(root)) throw \'core loop\'
    roots.push(root);
    if (root === protocore) break;
   }
   if (!roots.includes(protocore)) roots.push(protocore)
   return roots;
  }',
 "https://core.parts/proxy/beta/set.js" => '
  (_, kireji, value) => {
   console.warn("try to use the other method directly", { kireji, value, υ })
   return Ω[Ω[Ω[υ].query(l => l.kireji === kireji ? l.url : undefined)[0]]] = value
  }',
 "https://core.parts/proxy/beta/toPrimitive.js" => '
  (hint, caller) => {
   // console.groupCollapsed("toPrimitive", { υ, hint, caller })
   const core_root = "https://core.parts/core-part/", imagine = hint === "imagine"
   let primitive = Δ[υ];
   if (imagine || primitive === undefined) {
    const proxy = Ω[υ], constructor = proxy.constructor?.toPrimitive(), Kireji = new Map(), roots = β.rootsOf()
    if (!constructor) {
     const clone = proxy.core.toPrimitive()
     console.info("Missing constructor. Becoming clone.", { υ, clone })
     // console.groupEnd()
     return clone
    }
    for (const url in Δ) {
     if (!url.match(/^[^?]*\?\w*$/)) continue
     let p, r = p = υ, rank = 0;
     const [base, π] = url.split("?")
     do {
      if (r === base) {
       if (!Kireji.has(π) || Kireji.get(π)[0] > rank) {
        Kireji.set(π, [rank, `"${Δ[url]}": ${π}`]);
       }
       break
      }
      if (r === core_root) break;
      p = r
      rank++;
      r = Δ[`${r}?core`] ?? core_root
     } while (r !== p)
    }
    const runtime = eval("({ \n " + [...Kireji.values()].map(x=>x[1]).join(\',\n \') + "\n}) => {\n " + constructor + "\n}");
    primitive = runtime(Ω);
    output_type = typeof primitive;
    if (output_type !== "string") {
     console.groupEnd()
     throw new TypeError(`output of ${υ} must be a primitive string (got ${output_type})`)
    }
    if (imagine) {
     // console.log("returning imagined primitive value", primitive)
     // console.groupEnd()
     return primitive
    }
    // console.log("storing real primitive value", primitive)
    Ω[υ] = primitive
   }
   // console.groupEnd()
   return primitive
  }',
 "https://core.parts/proxy/beta/toString.js" => '
  () => {
   return Δ[υ]
  }',
 "https://core.parts/proxy/beta/valueOf.js" => '
  () => {
   return Δ[υ]
  }',
 "https://ejaugust.com/research/wasm/test.js" => 'WebAssembly.instantiateStreaming(onfetch("https://core.parts/wasm/test.wasm")).then(_ => console.info(_.instance.exports))',
 "https://ejaugust.com/research/wasm/test.wasm" => 'AGFzbQEAAAABBwFgA39/fwADAgEABQMBAAEHDgIDbWVtAgAEZmlsbAAACg0BCwAgACABIAL8CwALAAoEbmFtZQIDAQAA',
 "https://core.parts/favicon.ico?core" => 'https://core.parts/apple-touch-icon.png',
 "https://ejaugust.com/favicon.ico?core" => 'https://core.parts/apple-touch-icon.png',
 "https://orenjinari.com/favicon.ico?core" => 'https://core.parts/apple-touch-icon.png',
 "https://kireji.app/favicon.ico?core" => 'https://core.parts/apple-touch-icon.png',
 "https://kireji.io/favicon.ico?core" => 'https://core.parts/apple-touch-icon.png',
 "https://68.103.68.155/favicon.ico?core" => 'https://core.parts/apple-touch-icon.png',
 "https://35.138.226.122/favicon.ico?core" => 'https://core.parts/apple-touch-icon.png',
];
eval('?>' . $Δ["https://core.parts/php/index.php"] . "<?php ");
