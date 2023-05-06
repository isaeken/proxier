(function() {
    if (window.XMLHttpRequest) {
        function parseURI(url) {
            const m = String(url).replace(/^\s+|\s+$/g, "").match(/^([^:\/?#]+:)?(\/\/(?:[^:@]*(?::[^:@]*)?@)?(([^:\/?#]*)(?::(\d*))?))?([^?#]*)(\?[^#]*)?(#[\s\S]*)?/);
            // authority = "//" + user + ":" + pass "@" + hostname + ":" port
            return (m ? {
                href : m[0] || "",
                protocol : m[1] || "",
                authority: m[2] || "",
                host : m[3] || "",
                hostname : m[4] || "",
                port : m[5] || "",
                pathname : m[6] || "",
                search : m[7] || "",
                hash : m[8] || ""
            } : null);
        }

        function rel2abs(base, href) {
            function removeDotSegments(input) {
                const output = [];
                input.replace(/^(\.\.?(\/|$))+/, "")
                    .replace(/\/(\.(\/|$))+/g, "/")
                    .replace(/\/\.\.$/, "/../")
                    .replace(/\/?[^\/]*/g, function (p) {
                        if (p === "/..") {
                            output.pop();
                        } else {
                            output.push(p);
                        }
                    });
                return output.join("").replace(/^\//, input.charAt(0) === "/" ? "/" : "");
            }

            href = parseURI(href || "");
            base = parseURI(base || "");

            return !href || !base ? null : (href.protocol || base.protocol) +
                (href.protocol || href.authority ? href.authority : base.authority) +
                removeDotSegments(href.protocol || href.authority || href.pathname.charAt(0) === "/" ? href.pathname : (href.pathname ? ((base.authority && !base.pathname ? "/" : "") + base.pathname.slice(0, base.pathname.lastIndexOf("/") + 1) + href.pathname) : base.pathname)) +
                (href.protocol || href.authority || href.pathname ? href.search : (href.search || base.search)) +
                href.hash;
        }

        const proxyUrl = window.XMLHttpRequest.prototype.open;

        window.XMLHttpRequest.prototype.open = function () {
            if (arguments[1] !== null && arguments[1] !== undefined) {
                let url = arguments[1];
                url = rel2abs("{{url}}", url);
                url = "{{prefix}}" + url;
                arguments[1] = url;
            }

            return proxyUrl.apply(this, [].slice.call(arguments));
        };
    }
})();
