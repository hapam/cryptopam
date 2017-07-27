var shortPass = '<span class="pass_strong" id="pass_short">Quá ngắn</span>';
var badPass = '<span class="pass_strong" id="pass_bad">Không an toàn</span>';
var goodPass = '<span class="pass_strong" id="pass_good">Khá tốt</span>';
var strongPass = '<span class="pass_strong" id="pass_strong">Bảo mật cao</span>';

function passwordStrength(a, b) {
  var score = 0;
  if (a.length < 4) return shortPass
  if (a.toLowerCase() == b.toLowerCase()) return badPass
  score += a.length * 4;
  score += (checkRepetition(1, a).length - a.length) * 1;
  score += (checkRepetition(2, a).length - a.length) * 1;
  score += (checkRepetition(3, a).length - a.length) * 1;
  score += (checkRepetition(4, a).length - a.length) * 1;
  if (a.match(/(.*[0-9].*[0-9].*[0-9])/)) score += 5
  if (a.match(/(.*[!,@,#,$,%,^,&,*,?,_,~].*[!,@,#,$,%,^,&,*,?,_,~])/)) score += 5
  if (a.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) score += 10
  if (a.match(/([a-zA-Z])/) && a.match(/([0-9])/)) score += 15
  if (a.match(/([!,@,#,$,%,^,&,*,?,_,~])/) && a.match(/([0-9])/)) score += 15
  if (a.match(/([!,@,#,$,%,^,&,*,?,_,~])/) && a.match(/([a-zA-Z])/)) score += 15
  if (a.match(/^\w+$/) || a.match(/^\d+$/)) score -= 10
  if (score < 0) score = 0
  if (score > 100) score = 100
  if (score < 34) return badPass
  if (score < 68) return goodPass
  return strongPass
}
function checkRepetition(a, b) {
  var res = "";
  for (i = 0; i < b.length; i++) {
	repeated = true
	for (j = 0; j < a && (j + i + a) < b.length; j++)
	  repeated = repeated && (b.charAt(j + i) == b.charAt(j + i + a))
	if (j < a) repeated = false
	if (repeated) {
	  i += a - 1
	  repeated = false
	} else {
	  res += b.charAt(i)
	}
  }
  return res
}