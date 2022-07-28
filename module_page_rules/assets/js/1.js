$('body').on('click', '.rules_category', function (e) {
	$(".rules_block").hide(),
		$("#rule_" + $(this).data("id")).show(),
		$(".rules_category").removeClass("active-rule"),
		$(this).addClass("active-rule");
});

function delay(callback, ms) {
	var timer = 0;
	return function () {
		var context = this,
			args = arguments;
		clearTimeout(timer);
		timer = setTimeout(function () {
			callback.apply(context, args);
		}, ms || 0);
	};
}
$('#search_rule').keyup(delay(function (e) {
	$.ajax({
		type: "POST",
		url: location.href,
		data: "func=get&val=" + this.value,
		success: function (e) {
			//Объявление переменных
			let cardleft = $(".rules_card_left.home"),
				cardright = $(".rules_card_right.home");

			//Убираем предыдущие элементы с поиска
			$(".rules_card_left.add").remove();
			$(".rules_card_right.add").remove();

			//Если запрос вернул результат
			if (e != 'null') {
				let json = $.parseJSON(e),

					//Этакий костыль, чтобы добавленные элементы начинались с 900
					id = 900;

				//Скрытие начальных блоков
				cardleft.hide(), cardright.hide();

				//Добавление элементов поиска, тоже костыль
				$(".rules_card").append(`<div class="rules_card_left add"></div><div class="rules_card_right add"></div>`);
				for (var key in json) {
					//Увелечение id на 1
					id++;

					//Добавление категорий
					$(".rules_card_left.add").append(`<a href="#" class="rules_category" data-id="` + id + `">` + key + `</a>`);

					//Объявление значений категории
					var value = json[key];

					//Добавление header'a категории
					$(".rules_card_right.add").append(`<div class="rules_block" id="rule_` + id + `"><h2 class="rules_header text-center">` + key + `</h2></div>`);

					//Добавление элементов
					for (a = 0; a < value.length; a++) {
						$("#rule_" + id).append(`<p>` + value[a]['num'] + ` ` + value[a]['val'] + `</p>`)
					}
				}
			} else {
				//Возвращаем, если результат был херовый
				cardleft.show(), cardright.show();
			}
		}
	})
}, 500));