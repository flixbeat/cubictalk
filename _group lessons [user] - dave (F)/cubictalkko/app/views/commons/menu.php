<?php
$cubictalkko_path = c_path();
$cubicboardko_path = b_path();
echo $javascript->link('Utils.js');
?>

<div class="div-container">
	<div style="height: 2px;"></div>
	<img src="<?php echo $cubictalkko_path;?>/image/left-menu-en.png" border="0" alt="Menu" title="Menu">
	<div style="border:1px solid #ccc;width:202px;">
		<table class="" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
			<tr><td colspan="2" height="2" style="padding:0"></td></tr>
			<tr>
				<td class="text-left"><a href="<?php echo $cubictalkko_path;?>/lessons/mypage/<?php echo $loginInfo['User']['id']?>" style="margin-left:1.5em;">
					<img src="<?php echo $cubictalkko_path;?>/image/icon/lessons-mypage-icon.png" border="0" alt="수업일정" title="Mypage"></a></td>
				<td class="text-right"><a href="<?php echo $cubictalkko_path;?>/teachers/index/" style="margin-right:1.5em;">
					<img src="<?php echo $cubictalkko_path;?>/image/icon/teachers-reservation-icon.png" border="0" alt="수업예약" title="Reservation"></a></td>
			</tr>
			<tr><td colspan="2" height="2" style="padding:0"></td></tr>
			<tr><td colspan="2" height="1" style="background-color:#ccc;padding:0"></td></tr>
			<tr><td colspan="2" height="2" style="padding:0"></td></tr>
			<tr>
				<td class="text-left"><a href="<?php echo $cubictalkko_path;?>/lessons/myhistory/<?php echo $loginInfo['User']['id']?>" style="margin-left:1.5em;">
					<img src="<?php echo $cubictalkko_path;?>/image/icon/lessons-lesson_history-icon.png" border="0" alt="지난수업" title="Lessons History"></a></td>
				<td class="text-right"><a href="<?php echo $cubictalkko_path;?>/leveltests/leveltest/" style="margin-right:1.5em;">
					<img src="<?php echo $cubictalkko_path;?>/image/icon/leveltests-icon.png" border="0" alt="레벨테스트" title="Leveltest"></a></td>
			</tr>
			<tr><td colspan="2" height="2" style="padding:0"></td></tr>
			<tr><td colspan="2" height="1" style="background-color:#ccc;padding:0"></td></tr>
			<tr><td colspan="2" height="2" style="padding:0"></td></tr>
			<tr>
				<td class="text-left"><a href="<?php echo $cubicboardko_path;?>/reading.php" style="margin-left:1.5em;">
					<img src="<?php echo $cubictalkko_path;?>/image/icon/library-icon.png" border="0" alt="전자도서관" title="Library"></a></td>
				<td class="text-right"><a href="<?php echo $cubictalkko_path;?>/payments/" style="margin-right:1.5em;">
					<img src="<?php echo $cubictalkko_path;?>/image/icon/payments-icon.png" border="0" alt="결제내역" title="Payments History"></a></td>
			</tr>
			<tr><td colspan="2" height="2" style="padding:0"></td></tr>
			<tr><td colspan="2" height="1" style="background-color:#ccc;padding:0"></td></tr>
			<tr><td colspan="2" height="2" style="padding:0"></td></tr>
			<tr>
				<td class="text-left"><a href="<?php echo $cubictalkko_path;?>/bookmarks/mybookmark/" style="margin-left:1.5em;">
					<img src="<?php echo $cubictalkko_path;?>/image/icon/bookmarks-icon.png" border="0" alt="책설정" title="Book Settings"></a></td>
				<td class="text-right"><a href="<?php echo $cubictalkko_path;?>/fixedschedules/index1/" style="margin-right:1.5em;">
					<img src="<?php echo $cubictalkko_path;?>/image/icon/fixedschedules-icon.png" border="0" alt="고정수업" title="Fix Schedules(Booking)"></a></td>
			</tr>
			<tr><td colspan="2" height="2" style="padding:0"></td></tr>
			<tr><td colspan="2" height="1" style="background-color:#ccc;padding:0"></td></tr>
			<tr><td colspan="2" height="2" style="padding:0"></td></tr>
			<tr>
				<td class="text-left"><a href="<?php echo $cubictalkko_path;?>/styles/index/" style="margin-left:1.5em;">
					<img src="<?php echo $cubictalkko_path;?>/image/icon/styles-icon.png" border="0" alt="수업스타일" title="Set my class style!"></a></td>
				<td class="text-right"><a href="<?php echo $cubictalkko_path;?>/complains/search/" style="margin-right:1.5em;">
					<img src="<?php echo $cubictalkko_path;?>/image/icon/complains-icon.png" border="0" alt="수업불만" title="Complaints"></a></td>
			</tr>
			<tr><td colspan="2" height="2" style="padding:0"></td></tr>
			<tr><td colspan="2" height="1" style="background-color:#ccc;padding:0"></td></tr>
			<tr><td colspan="2" height="2" style="padding:0"></td></tr>
			<tr>
				<td class="text-left"><a href="<?php echo $cubictalkko_path;?>/favorites/teacher/" style="margin-left:1.5em;">
					<img src="<?php echo $cubictalkko_path;?>/image/icon/favorite_teachers-icon.png" border="0" alt="강사 북마크" title="Bookmark favorite teachers!"></a></td>
				<td class="text-right"><a href="<?php echo $cubictalkko_path;?>/homeworks/index/" style="margin-right:1.5em;">
					<img src="<?php echo $cubictalkko_path;?>/image/icon/homeworks-icon.png" border="0" alt="숙제" title="Homeworks!"></a></td>
			</tr>
			<tr><td colspan="2" height="2" style="padding:0"></td></tr>
			<tr><td colspan="2" height="1" style="background-color:#ccc;padding:0"></td></tr>
			<tr><td colspan="2" height="2" style="padding:0"></td></tr>
			<tr>
				<td class="text-left"><a href="<?php echo $cubictalkko_path;?>/extendeds/" style="margin-left:1.5em;">
					<img src="<?php echo $cubictalkko_path;?>/image/icon/extendeds-icon.png" border="0" alt="연장내역" title="Extensions History"></a></td>
				<td class="text-right"><a href="<?php echo $cubictalkko_path;?>/friends/" style="margin-right:1.5em;">
					<img src="<?php echo $cubictalkko_path;?>/image/icon/friends-icon.png" border="0" alt="친구소개" title="Introduce friends"></a></td>
			</tr>
			<tr><td colspan="2" height="2" style="padding:0"></td></tr>
			<tr><td colspan="2" height="1" style="background-color:#ccc;padding:0"></td></tr>
			<tr><td colspan="2" height="2" style="padding:0"></td></tr>
			<tr>
				<td class="text-left"><a href="<?php echo $cubictalkko_path;?>/compositions/composition/" style="margin-left:1.5em;">
					<img src="<?php echo $cubictalkko_path;?>/image/icon/compositions-icon.png" border="0" alt="작문" title="Compositions"></a></td>
				<td class="text-right"><a href="<?php echo $cubictalkko_path;?>/group_lessons/" style="margin-right:1.5em;">
					<img src="<?php echo $cubictalkko_path;?>/image/icon/groupclass-en-icon.png" border="0" alt="친구소개" title="Group Class"></a></td>

			</tr>
			<tr></tr>
			<tr><td colspan="2" height="1" style="background-color:#ccc;padding:0"></td></tr>
			<tr><td colspan="2" height="2" style="padding:0"></td></tr>
			<tr>
				<td colspan="2" class="text-center">
					<img src="<?php echo $cubicboardko_path;?>/image/icons/question.png" border="0" width="200" height="42" usemap="#map">
					<map name="map">
						<area shape="rect" coords="0,0,62,41" href="<?php echo $cubicboardko_path;?>/bbs/board.php?bo_table=FAQKO" alt="">
						<area shape="rect" coords="67,0,134,41" href="<?php echo $cubicboardko_path;?>/bbs/board.php?bo_table=questionKO" alt="">
						<area shape="rect" coords="136,0,204,41" href="<?php echo $cubicboardko_path;?>/bbs/board.php?bo_table=remittanceKO" alt="">
					</map>
				</td>
			</tr>
			<tr><td colspan="2" height="2" style="padding:0"></td></tr>
		</table>
	</div>
</div>
